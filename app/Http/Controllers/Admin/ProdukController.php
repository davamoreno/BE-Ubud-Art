<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Produk;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchProdukRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProdukResource;
use App\Http\Requests\Admin\StoreProdukRequest;
use App\Http\Requests\Admin\UpdateProdukRequest;
use App\Queries\ProdukQuery;

class ProdukController extends Controller
{
    public function index(SearchProdukRequest $request)
    {
        $filters = $request->validated();
        $produks = ProdukQuery::filter($filters)->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => ProdukResource::collection($produks),
            'meta' => [
                 'current_page' => $produks->currentPage(),
                'last_page' => $produks->lastPage(),
                'per_page' => $produks->perPage(),
                'total' => $produks->total(),
            ]
        ]); 
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('produk', 'public');
            $data['image'] = $path;
        }

        $produk = Produk::create($data);

        $tagIds = $request->input('tags'); 

    // Pastikan $tagIds tidak null sebelum digunakan
    if (empty($tagIds)) {
        $tagIds = [];
    }

    // Buat produk tanpa field tags terlebih dahulu
    $product = Produk::create($request->except('tags'));

    // Lalu sinkronkan relasi many-to-many
    $product->tags()->sync($tagIds);

        return new ProdukResource($produk);
    }

    public function show($slug)
    {
        $produk = Produk::with(['toko', 'kategori', 'tags'])->where('slug', $slug)->first();

        if (!$produk) {
            return response()->json([
                'message' => 'Produk not found'
            ], 404);
        }

        return new ProdukResource($produk);
    }

    public function update(UpdateProdukRequest $request, $slug)
    {
        $produk = Produk::where('slug', $slug)->first();
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

        return new ProdukResource($produk->load(['kategori', 'toko', 'tags']));
    }

    public function destroy($slug)
    {
        $produk = Produk::where('slug', $slug)->first();
        if ($produk->image && Storage::disk('public')->exists($produk->image)) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();
        return response()->json(['message' => 'Produk deleted successfully']);
    }
}
