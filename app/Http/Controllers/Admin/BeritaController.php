<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use App\Queries\BeritaQuery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\BeritaResource;
use App\Http\Requests\Admin\StoreBeritaRequest;
use App\Http\Requests\Admin\SearchBeritaRequest;
use App\Http\Requests\Admin\UpdateBeritaRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BeritaController extends Controller
{
    use ValidatesRequests, AuthorizesRequests;
    // Tampilkan semua berita
       public function index(SearchBeritaRequest $request)
    {
        // Otorisasi (jika perlu)
        // $this->authorize('viewAny', Berita::class);
        
        $filters = $request->validated();
        
        // --- PERBAIKAN UTAMA DI SINI ---
        // 1. Ambil nilai 'per_page' dari filter yang sudah divalidasi.
        // 2. Jika tidak ada, gunakan nilai default (misalnya 10).
        $perPage = $filters['per_page'] ?? 10;

        // Gunakan variabel $perPage di dalam method paginate()
        $berita = BeritaQuery::filter($filters)->paginate($perPage);
                              
        return BeritaResource::collection($berita) 
            ->additional([
                'success' => true,
                'message' => 'Berita retrieved successfully'
            ]);
    }

    // Simpan berita baru
    public function store(StoreBeritaRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('berita', 'public');
        }

        // $data['user_id'] = auth()->id();

        $berita = Berita::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil ditambahkan.',
            'data' => new BeritaResource($berita)
        ], 201);
    }

    // Detail berita
    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->first();

        return response()->json([
            'success' => true,
            'data' => new BeritaResource($berita)
        ]);
    }

    // Update berita
    public function update(UpdateBeritaRequest $request, Berita $berita)
    {
        $this->authorize('update', $berita);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($berita->image && Storage::disk('public')->exists($berita->image)) {
                Storage::disk('public')->delete($berita->image);
            }

            $data['image'] = $request->file('image')->store('berita', 'public');
        }

        $berita->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui.',
            'data' => new BeritaResource($berita)
        ]);
    }

    // Hapus berita
    public function destroy(Berita $berita)
    {
        if ($berita->image && Storage::disk('public')->exists($berita->image)) {
            Storage::disk('public')->delete($berita->image);
        }

        $berita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus.'
        ]);
    }
}
