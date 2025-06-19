<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    }
    
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $i = 1;

        while (Produk::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
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

}
