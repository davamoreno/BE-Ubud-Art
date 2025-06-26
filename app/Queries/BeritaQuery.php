<?php
namespace App\Queries;

use App\Models\Berita;
use Illuminate\Database\Eloquent\Builder;

class BeritaQuery
{
    public static function filter(array $filters): Builder
    {
        return Berita::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            });
    }
}