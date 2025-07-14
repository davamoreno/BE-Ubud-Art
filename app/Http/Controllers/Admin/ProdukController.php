<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Produk;
use App\Queries\ProdukQuery;
use App\Jobs\LogProductViewJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProdukResource;
use App\Http\Requests\Admin\StoreProdukRequest;
use App\Http\Requests\Admin\SearchProdukRequest;
use App\Http\Requests\Admin\UpdateProdukRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(SearchProdukRequest $request)
    {
        $this->authorize('viewAny', Produk::class);
        $filters = $request->validated();
        // Semua keajaiban sorting dan filtering terjadi di dalam baris ini!
        $produks = ProdukQuery::filter($filters)->paginate(10);
        return ProdukResource::collection($produks);
    }

    public function store(StoreProdukRequest $request)
    {
        $this->authorize('create', Produk::class);
        // Validasi dan ambil data dari request
        $data = $request->validated();

        // 3. Handle file upload jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produk', 'public');
        }

        // 4. Buat produk HANYA SATU KALI dan simpan di variabel $produk
        $produk = Produk::create($data);

        // 5. Sinkronkan tags ke produk yang BARU SAJA dibuat
        if ($request->has('tags')) {
            $produk->tags()->sync($request->input('tags', []));
        }

        // 2. KIRIM "LONCENG" SETELAH BERHASIL
        Redis::publish('recommendation-updates', 'refresh');

        return new ProdukResource($produk->load(['kategori', 'toko', 'tags']));
    }


    public function show(Produk $produk)
    {
        // Otorisasi pada objek yang sudah pasti ditemukan
        $this->authorize('view', $produk);

        // Catat jejak pengguna
        if (auth('api')->check()) {
            LogProductViewJob::dispatch(auth('api')->id(), $produk->id);
        }

        $rekomendasiProduk = collect();
        try {
            // "Bertanya" ke servis Flask untuk rekomendasi
            $response = Http::post('http://127.0.0.1:5000/recommend', [
                'product_id' => $produk->id,
            ]);

            if ($response->successful()) {
                $rekomendasiIds = $response->json()['recommendations'];
                if (!empty($rekomendasiIds)) {
                    $rekomendasiProduk = Produk::whereIn('id', $rekomendasiIds)->get();
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal menghubungi servis rekomendasi: ' . $e->getMessage());
        }

        return response()->json([
            // Tidak perlu ->load() lagi karena kita bisa eager load di resource
            'data' => new ProdukResource($produk->load(['toko', 'kategori', 'tags'])),
            'rekomendasi' => ProdukResource::collection($rekomendasiProduk)
        ]);
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $this->authorize('update', $produk);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($produk->image && Storage::disk('public')->exists($produk->image)) {
                Storage::disk('public')->delete($produk->image);
            }
            $data['image'] = $request->file('image')->store('produk', 'public');
        }

        $produk->update($data);
        if ($request->has('tags')) {
            $produk->tags()->sync($request->input('tags', []));
        }

        // 2. KIRIM "LONCENG" SETELAH BERHASIL
        Redis::publish('recommendation-updates', 'refresh');

        return new ProdukResource($produk->load(['kategori', 'toko', 'tags']));
    }

    public function destroy($slug)
    {
        $produk = Produk::where('slug', $slug)->first();
        $this->authorize('delete', $produk);
        if ($produk->image && Storage::disk('public')->exists($produk->image)) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();

        Redis::publish('recommendation-updates', 'refresh');

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }

    public function getRandomProducts(Request $request)
    {
         // Validasi sederhana untuk parameter 'limit' agar aman
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:20'
        ]);
        
        $limit = $validated['limit'] ?? 8; // Default 8 produk

        // --- PERBAIKAN UTAMA DI SINI ---
        
        // 1. Kita mulai query langsung dari Model.
        $query = Produk::query();

        // 2. JURUS PAMUNGKAS: Perintahkan Eloquent untuk mengabaikan semua
        //    Global Scope yang mungkin menambahkan 'orderBy' secara diam-diam.
        $query->withoutGlobalScopes();

        // 3. (Opsional) Kita masih bisa menambahkan filter lain jika perlu
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->input('kategori_id'));
        }
        if ($request->has('tags')) {
            $tagIds = $request->input('tags');
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            }, '=', count($tagIds));
        }

        // 4. Terapkan pengacakan dan ambil hasilnya.
        $produks = $query->inRandomOrder()->limit($limit)->get();

        // 5. Kembalikan dengan header no-cache untuk keamanan ganda.
        return ProdukResource::collection($produks)
            ->response()
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
