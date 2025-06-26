<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'detail' => $this->detail,
            'image' => $this->image,
            'toko' => [
                'id' => $this->toko->id,
                'nama' => $this->toko->nama,
            ],
            'kategori' => [
                'id' => $this->kategori->id,
                'nama' => $this->kategori->nama,
            ],
            'tags' => $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'nama' => $tag->nama,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
