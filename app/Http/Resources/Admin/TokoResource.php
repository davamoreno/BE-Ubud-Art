<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id ?? null,
            'nama' => $this->nama ?? null,
            'slug' => $this->slug ?? null,
            'deskripsi' => $this->deskripsi ?? null,
            'telepon' => $this->telepon ?? null,
            'link' => $this->link ?? null,
            'image' => $this->image ?? null,
            'status' => $this->status ?? null,
            'rating' => (float) $this->rating,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'produks' => [
                'data' => $this->produks->map(function ($produk) {
                    return [
                        'id' => $produk->id,
                        'title' => $produk->title,
                        'slug' => $produk->slug,
                        'image' => $produk->image,
                        'toko_nama' => $produk->toko->nama ?? null,
                        'deskripsi' => $produk->deskripsi,
                        'detail' => $produk->detail,
                        'tags' => $produk->tags->pluck('nama'),
                        'kategori' => $produk->kategori ? [
                            'id' => $produk->kategori->id,
                            'nama' => $produk->kategori->nama,
                        ] : null,
                        'created_at' => optional($produk->created_at)->toDateTimeString(),
                        'updated_at' => optional($produk->updated_at)->toDateTimeString(),
                    ];
                })
            ]
        ];
    }
}
