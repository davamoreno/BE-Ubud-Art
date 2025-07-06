<?php

namespace App\Queries;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;

class ProdukQuery
{
    protected Builder $query;
    protected array $filters;

    public static function filter(array $filters = []): self
    {
        return new static($filters);
    }

    public function __construct(array $filters)
    {
        $this->query = Produk::query();
        $this->filters = $filters;

        $this->applyFilters();
    }

    protected function applyFilters(): void
    {
        // Filter pencarian yang mungkin sudah ada
        if (isset($this->filters['search'])) {
            $this->query->where('title', 'like', '%' . $this->filters['search'] . '%');
        }

        // --- TAMBAHKAN LOGIKA FILTER TAG DI SINI ---
        if (isset($this->filters['tags']) && is_array($this->filters['tags'])) {
            $tagIds = $this->filters['tags'];

            // Logika ini memastikan produk memiliki SEMUA tag yang dipilih.
            // Ia akan mencari produk yang relasi 'tags'-nya mengandung
            // semua ID dari $tagIds.
            $this->query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            }, '=', count($tagIds));
        }

        // --- TAMBAHKAN LOGIKA SORTING DI SINI ---
        $sortOrder = $this->filters['sort'] ?? 'newest'; // Default sort adalah 'newest'

        match ($sortOrder) {
            'newest' => $this->query->latest(),
            'oldest' => $this->query->oldest(),
            'asc' => $this->query->orderBy('title', 'asc'),
            'desc' => $this->query->orderBy('title', 'desc'),
            'highest' => $this->query->orderBy('rating', 'desc'),
            'lowest' => $this->query->orderBy('rating', 'asc'),
            default => $this->query->latest(),
        };

        if (isset($this->filters['kategori'])) {
            $this->query->where('kategori_id', $this->filters['kategori']);
        }

    }

    // Method untuk mengambil hasil query
    public function get()
    {
        return $this->query->get();
    }

    public function paginate(int $perPage = 15)
    {
        // Gunakan per_page dari filter jika ada, jika tidak gunakan default
        $perPage = $this->filters['per_page'] ?? $perPage;
        return $this->query->paginate($perPage);
    }

    // Anda bisa menambahkan method lain jika perlu
}
