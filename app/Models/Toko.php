<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Toko extends Model
{

    //Data Yang Bisa Terisi
    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'image',
        'telepon',
        'user_id',
        'rating', // Rata-rata rating dari semua produk
        'products_count' // Jumlah produk yang dimiliki
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    protected static function booted()
    {
        static::creating(function ($toko) {
            $toko->slug = $toko->generateSlug();
        });

        static::updating(function ($toko) {
            if ($toko->isDirty('nama')) {
                $toko->slug = $toko->generateSlug();
            }
        });

    }
    
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->nama);
        $slug = $baseSlug;
        $i = 1;

        $query = Toko::where('slug', $slug);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        while (Toko::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;

            $query = Toko::where('slug', $slug);

            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }
        }

        return $slug;
    }

    public function recalculateRating(): void
    {
        // Ambil rata-rata rating dari semua produk yang 'rating'-nya lebih dari 0
        $this->rating = $this->produks()
                             ->where('rating', '>', 0)
                             ->avg('rating') ?? 0;
        
        // Hitung jumlah produk yang dimiliki
        $this->products_count = $this->produks()->count();

        // Simpan perubahan ke database
        $this->save();
    }

    //Relasi :

    // Many To Many
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // Many To One
    public function produks() : HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
