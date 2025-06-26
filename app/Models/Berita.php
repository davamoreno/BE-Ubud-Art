<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Berita extends Model
{
    protected $fillable = [
        'title',
        'deskripsi',
        'image',
        'slug',
        'user_id'
    ];

    protected $policies = [
        \App\Models\Berita::class => \App\Policies\BeritaPolicy::class
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($berita) {
            $berita->slug = $berita->generateSlug();
        });

        static::updating(function ($berita) {
            if ($berita->isDirty('title')) {
                $berita->slug = $berita->generateSlug();
            }
        });

    }


    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $i = 1;

        $query = Berita::where('slug', $slug);

        if($this->exists)
        {
            $query->where('id', '!=', $this->id);
        }

        while (Berita::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;

            $query = Berita::where('slug', $slug);
            if($this->exists)
            {
                $query->where('id', '!=', $this->id);
            }
        }

        return $slug;
    }
}
