<?php

namespace App\Http\Controllers\Admin;

use App\Models\Toko;
use App\Queries\TokoQuery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\TokoResource;
use App\Http\Requests\Admin\StoreTokoRequest;
use App\Http\Requests\Admin\SearchTokoRequest;
use App\Http\Requests\Admin\UpdateTokoRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TokoController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function index(SearchTokoRequest $request)
    {
        $this->authorize('viewAny', Toko::class);
        $filters = $request->validated();
        $tokos = TokoQuery::filter($filters)->latest()->paginate(10);
        return TokoResource::collection($tokos);
    }

    public function store(StoreTokoRequest $request)
    {
        $this->authorize('create', Toko::class);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('toko', 'public');
        }
        $toko = Toko::create($data);
        return new TokoResource($toko);
    }

    // PERBAIKAN: Menggunakan Route-Model Binding
    public function show(Toko $toko)
    {
        $this->authorize('view', $toko);
        return new TokoResource($toko->load('produks')); // Eager load produknya
    }

    // PERBAIKAN: Menggunakan Route-Model Binding
    public function update(UpdateTokoRequest $request, Toko $toko)
    {
        $this->authorize('update', $toko);
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

    // PERBAIKAN: Menggunakan Route-Model Binding
    public function destroy(Toko $toko)
    {
        $this->authorize('delete', $toko);
        if ($toko->image && Storage::disk('public')->exists($toko->image)) {
            Storage::disk('public')->delete($toko->image);
        }
        $toko->delete();
        return response()->json(['message' => 'Toko berhasil dihapus.']);
    }
}