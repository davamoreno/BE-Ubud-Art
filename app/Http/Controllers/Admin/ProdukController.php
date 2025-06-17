<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProdukResource;
use App\Http\Requests\Admin\StoreProdukRequest;
use App\Http\Requests\Admin\UpdateProdukRequest;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::paginate(10);
        return ProdukResource::collection($produks);
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('produk', 'public');
            $data['image'] = $path;
        }

        $produk = Produk::create($data);
        return new ProdukResource($produk);
    }

    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk not found'
            ], 404);
        }

        return new ProdukResource($produk);
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($produk->image && Storage::disk('public')->exists($produk->image)) {
                Storage::disk('public')->delete($produk->image);
            }

            $data['image'] = $request->file('image')->store('produk', 'public');
        }

        $produk->update($data);
        return new ProdukResource($produk);
    }

    public function destroy(Produk $produk)
    {
        if ($produk->image && Storage::disk('public')->exists($produk->image)) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();
        return response()->json(['message' => 'Produk deleted successfully']);
    }
}
