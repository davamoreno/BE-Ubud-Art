<?php
namespace App\Queries;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;

class ProdukQuery
{
    public static function filter(array $filters): Builder
    {
        return Produk::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        });
    }
}