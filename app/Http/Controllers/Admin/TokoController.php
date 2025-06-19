<?php

namespace App\Http\Controllers\Admin;

use App\Models\Toko;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\TokoResource;
use App\Http\Requests\Admin\StoreTokoRequest;
use App\Http\Requests\Admin\UpdateTokoRequest;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::paginate(10);
        return TokoResource::collection($tokos);
    }

    public function store(StoreTokoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('toko', 'public');
            $data['image'] = $path;
        }

        $toko = Toko::create($data);
        return new TokoResource($toko);
    }

    public function show($slug)
    {
        $toko = Toko::Where('slug', $slug)->first();
    
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

    public function update(UpdateTokoRequest $request, $slug)
    {
        $toko = Toko::where('slug', $slug)->first();
        $data = $request->validated();

        if ($request->hasFile('image')) {
           if ($toko->image && Storage::disk('public')->exists($toko->image)) {
                Storage::disk('public')->delete($toko->image);
            }

            $data['image'] = $request->file('image')->store('toko', 'public');
        }

        $toko->update($data);
        return new TokoResource($toko);
    }

    public function destroy($slug)
    {
        $toko = Toko::where('slug', $slug)->first();   
        if ($toko->image && Storage::disk('public')->exists($toko->image)) {
            Storage::disk('public')->delete($toko->image);
        }

        $toko->delete();
        return response()->json(['message' => 'Toko deleted successfully']);
    }
}
