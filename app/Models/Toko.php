<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Toko extends Model
{

    //Data Yang Bisa Terisi
    protected $fillable = [
        'nama',
        'nama_pemilik',
        'nomor_toko',
        'lantai',
        'telepon',
        'user_id'
    ];
    
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
