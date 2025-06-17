<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Http\Requests\Admin\StoreTokoRequest;
use App\Http\Requests\Admin\UpdateTokoRequest;
use App\Http\Resources\Admin\TokoResource;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::all();
        return TokoResource::collection($tokos);
    }

    public function store(StoreTokoRequest $request)
    {
        $toko = Toko::create($request->validated());
        return new TokoResource($toko);
    }

    public function show($id)
    {
        $toko = Toko::find($id);
    
        if (!$toko) {
            return response()->json([
                'message' => 'Toko not found'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => new TokoResource($toko)
        ]);
    }

    public function update(UpdateTokoRequest $request, Toko $toko)
    {
        $toko->update($request->validated());
        return new TokoResource($toko);
    }

    public function destroy(Toko $toko)
    {
        $toko->delete();
        return response()->json(['message' => 'Toko deleted successfully']);
    }
}
