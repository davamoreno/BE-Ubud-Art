<?php
namespace App\Queries;

use App\Models\Toko;
use Illuminate\Database\Eloquent\Builder;

class TokoQuery
{
    public static function filter(array $filters): Builder
    {
        return Toko::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
        });
    }
}