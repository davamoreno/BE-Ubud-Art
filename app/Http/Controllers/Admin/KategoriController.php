<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Http\Requests\Admin\UpdateKategoriRequest;
use App\Http\Resources\Admin\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::paginate(10);
        return KategoriResource::collection($kategori);
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());
        return new KategoriResource($kategori);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
    
        if (!$kategori) {
            return response()->json([
                'message' => 'kategori not found'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => new KategoriResource($kategori)
        ]);
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->validated());
        return new kategoriResource($kategori);
    }

    public function destroy(kategori $kategori)
    {
        $kategori->delete();
        return response()->json(['message' => 'Kategori deleted successfully']);
    }
}
