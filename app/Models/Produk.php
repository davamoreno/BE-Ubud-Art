<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Produk extends Model
{
    //Data Yang Bisa Terisi
    protected $fillable = [
        'title',
        'slug',
        'deskripsi',
        'detail',
        'image',
        'toko_id',
        'kategori_id'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($produk) {
            $produk->slug = $produk->generateSlug();
        });

        static::updating(function ($produk) {
            if ($produk->isDirty('title')) {
                $produk->slug = $produk->generateSlug();
            }
        });

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });

    }
    
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $i = 1;

        $query = Produk::where('slug', $slug);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        while (Produk::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
            $query = Produk::where('slug', $slug);

            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }
        }

        return $slug;
    }
    //Relasi :

    //One To Many
    public function toko() : BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function kategori() : BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'produk_tag');
    }

    //Many To One
    public function ratings() : HasMany
    {
        return $this->hasMany(Rating::class);
    }

     public function komentars() : HasMany
    {
        return $this->hasMany(Komentar::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Riview::class);
    }
    
    public function recalculateRating()
    {
        $reviews = $this->reviews(); // Asumsi relasi Anda bernama 'riviews'

        $this->rating = $reviews->avg('rating') ?? 0;
        $this->reviews_count = $reviews->count();
        $this->save();
    }

}
