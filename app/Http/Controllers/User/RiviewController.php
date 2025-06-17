<?php

namespace App\Http\Controllers\User;

use App\Models\Produk;
use App\Models\Riview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\RiviewResource;
use App\Http\Requests\User\StoreRiviewRequest;
use App\Http\Requests\User\UpdateRiviewRequest;

class RiviewController extends Controller
{
    public function index()
    {
        $reviews = Riview::with(['user', 'produk'])->paginate(10);
        return RiviewResource::collection($reviews);
    }

    public function store(StoreRiviewRequest $request, $produkId)
    {
        // Optional: Validasi apakah produk ada
        $produk = Produk::findOrFail($produkId);
    
        $review = Riview::create([
            'user_id' => auth()->id(),
            'produk_id' => $produk->id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);
    
        return new RiviewResource($review);
    }

    public function show($id)
    {
        $review = Riview::with(['user', 'produk'])->find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        return new RiviewResource($review);
    }

    public function update(UpdateRiviewRequest $request, Riview $review)
    {
        // Cek apakah user yang login adalah pemilik review
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->update($request->validated());
        return new RiviewResource($review);
    }


    public function destroy(Riview $review)
    {
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }
}
