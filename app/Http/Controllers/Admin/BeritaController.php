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
    use AuthorizesRequests, ValidatesRequests;
    
    // Tampilkan semua berita
    public function index(SearchBeritaRequest $request)
    {
        $filters = $request->validated();

        $beritas = BeritaQuery::filter($filters)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => BeritaResource::collection($beritas),
            'meta' => [
                'current_page' => $beritas->currentPage(),
                'last_page' => $beritas->lastPage(),
                'per_page' => $beritas->perPage(),
                'total' => $beritas->total(),
            ]
        ]);
    }


    // Simpan berita baru
    public function store(StoreBeritaRequest $request)
    {
        $this->authorize('create', Berita::class);
        
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('berita', 'public');
        }

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
    public function update(UpdateBeritaRequest $request, $slug)
    {
        $this->authorize('update', Berita::class);

        $berita = Berita::where('slug', $slug)->first();
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
        $this->authorize('delete', Berita::class);
        
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
