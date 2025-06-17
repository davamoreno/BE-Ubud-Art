<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    //Data Yang Bisa Terisi
    protected $fillable = [
        'title',
        'deskripsi',
        'detail',
        'image',
        'toko_id'
    ];

    //Relasi :

    //One To Many
    public function toko() : BelongsTo
    {
        return $this->belongsTo(Toko::class);
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
