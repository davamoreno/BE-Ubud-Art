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
        'user_id'
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
