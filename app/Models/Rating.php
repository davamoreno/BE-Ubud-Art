<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'rating',
        'user_id',
        'produk_id'
    ];

    // Relasi :

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function produk() : BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
