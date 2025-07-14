<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeritaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'deskripsi'     => $this->deskripsi,
            'image'         => $this->image ? asset('storage/' . $this->image) : null,
            'slug'          => $this->slug,
            'created_at'    => $this->created_at
        ];
    }
}
