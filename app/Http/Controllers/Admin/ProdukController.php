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


    public function show($slug)
    {  
        $produk = Produk::with(['toko', 'kategori', 'tags'])->where('slug', $slug)->first();

        $rekomendasiProduk = collect();

        // 2. GUNAKAN BLOK TRY-CATCH
        // Ini penting agar aplikasi Laravel tidak crash jika servis Flask sedang mati.
        try {
            // 3. PANGGIL/LEMPAR API KE FLASK MENGGUNAKAN Http FACADE
            // Alamat ini harus sesuai dengan alamat server Flask Anda berjalan.
            if (auth('api')->check()) {
                // Jalankan pencatatan di latar belakang
                LogProductViewJob::dispatch(auth('api')->id(), $produk->id);
            }
            $response = Http::post('http://127.0.0.1:5000/recommend', [
                'product_id' => $produk->id,
            ]);
        
            // 4. PROSES JAWABAN DARI FLASK
            if ($response->successful()) {
                $rekomendasiIds = $response->json()['recommendations'];
            
                // Ambil data produk lengkap dari database Laravel berdasarkan ID yang diterima
                if (!empty($rekomendasiIds)) {
                    $rekomendasiProduk = Produk::whereIn('id', $rekomendasiIds)->get();
                }
            }
        } catch (\Exception $e) {
            // Jika gagal terhubung ke servis Flask, catat errornya di log Laravel.
            // Aplikasi akan tetap berjalan dan hanya menampilkan rekomendasi kosong.
            Log::error('Gagal menghubungi servis rekomendasi: ' . $e->getMessage());
        }

        // 5. KEMBALIKAN SEMUA DATA DALAM SATU RESPON
        return response()->json([
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
        $this->authorize('delete', Produk::class);

        $produk = Produk::where('slug', $slug)->first();
        if ($produk->image && Storage::disk('public')->exists($produk->image)) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();

        Redis::publish('recommendation-updates', 'refresh');

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
