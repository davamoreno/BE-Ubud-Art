<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\ProdukResource;

class DashboardController extends Controller
{
    public function recommendations()
    {
        if (auth('api')->check()) {
            $userId = auth('api')->id();

            // Ambil rekomendasi berdasarkan score dari tabel user_dashboard_recommendations
            $rekomendasi = Produk::query()
                ->join('user_dashboard_recommendations as recs', 'produks.id', '=', 'recs.produk_id')
                ->where('recs.user_id', $userId)
                ->reorder()
                ->orderByDesc('recs.score')
                ->select('produks.*', 'recs.score', 'recs.created_at as recommended_at')
                ->limit(10)
                ->get();

            if ($rekomendasi->isNotEmpty()) {
                return ProdukResource::collection($rekomendasi);
            }
        }

        // Fallback jika belum login atau belum ada rekomendasi
        $fallbackProduk = Produk::query()
            ->reorder()
            ->orderByDesc('rating')
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        return ProdukResource::collection($fallbackProduk);
    }
}
