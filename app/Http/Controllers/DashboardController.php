<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProdukResource;

class DashboardController extends Controller
{
    public function recommendations()
    {
        // Cek apakah ada user yang sedang login melalui guard 'api'
        if (auth('api')->check()) {
            $userId = auth('api')->id();
            
            // Ambil ID rekomendasi yang sudah dihitung oleh Python dari tabel hasil
            $recommendedIds = DB::table('user_dashboard_recommendations')
                                ->where('user_id', $userId)
                                ->orderByDesc('score') // Urutkan berdasarkan skor tertinggi
                                ->limit(12) // Ambil 12 rekomendasi teratas
                                ->pluck('produk_id');

            // Jika user punya rekomendasi personal, tampilkan itu
            if ($recommendedIds->isNotEmpty()) {
                // Ambil data produk lengkapnya, dan JAGA URUTANNYA sesuai skor
                $rekomendasi = Produk::whereIn('id', $recommendedIds)
                                     ->orderByRaw(DB::raw("FIELD(id, ".implode(',', $recommendedIds->all()).")"))
                                     ->get();
                
                return ProdukResource::collection($rekomendasi);
            }
        }

        // --- Fallback atau Kondisi Default ---
        // Jika user belum login, atau user login tapi belum punya rekomendasi personal,
        // tampilkan produk dengan rating tertinggi sebagai gantinya.
        $rekomendasiPopuler = Produk::orderByDesc('rating')
                                    ->orderByDesc('reviews_count')
                                    ->limit(12)
                                    ->get();

        return ProdukResource::collection($rekomendasiPopuler);
    }
}
