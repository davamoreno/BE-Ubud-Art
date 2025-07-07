<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\BeritaResource;
use App\Http\Requests\Admin\StoreBeritaRequest;
use App\Http\Requests\Admin\UpdateBeritaRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BeritaController extends Controller
{
    use ValidatesRequests, AuthorizesRequests;
    // Tampilkan semua berita
    public function index()
    {
        $beritas = Berita::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => BeritaResource::collection($beritas)
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
    public function update(UpdateBeritaRequest $request, $slug)
    {
        $berita = Berita::where('slug', $slug)->first();
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
