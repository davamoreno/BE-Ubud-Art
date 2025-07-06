<?php

namespace App\Queries;

use App\Models\Riview;
use Illuminate\Database\Eloquent\Builder;

class RiviewQuery
{
    protected Builder $query;
    protected array $filters;

    public static function filter(array $filters = []): self
    {
        return new static($filters);
    }
    public function __construct(array $filters)
    {
        $this->query = Riview::query()->with(['user', 'produk']);

        $this->filters = $filters;

        $this->applyFilters();
    }
    protected function applyFilters(): void
    {
        if (isset($this->filters['produk_id'])) {
            $this->query->where('produk_id', $this->filters['produk_id']);
        }

        if (isset($this->filters['ratings']) && is_array($this->filters['ratings'])) {
            $ratings = $this->filters['ratings'];

            // Logika ini memastikan produk memiliki SEMUA rating yang dipilih.
            // Ia akan mencari produk yang relasi 'ratings'-nya mengandung
            // semua nilai dari $ratings.
            $this->query->whereIn('rating', $ratings);
        }
        // --- TAMBAHKAN LOGIKA SORTING DI SINI ---
        $sortOrder = $this->filters['sort'] ?? 'newest'; // Default sort adalah 'newest'

        match ($sortOrder) {
            'newest' => $this->query->latest('created_at'),
            'oldest' => $this->query->oldest('created_at'),
            'highest' => $this->query->orderBy('rating', 'desc'),
            'lowest' => $this->query->orderBy('rating', 'asc'),
            default => $this->query->latest(),
        };
    }
      public function get()
    {
        return $this->query->get();
    }

    public function paginate(int $perPage = 10)
    {
        return $this->query->paginate($perPage);
    }
}