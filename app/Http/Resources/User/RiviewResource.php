<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'        => $this->id,
            'user'      => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'produk'    => [
                'id' => $this->produk->id,
                'title' => $this->produk->title,
            ],
            'rating'    => $this->rating,
            'komentar'  => $this->komentar,
            'created_at'=> $this->created_at->toDateTimeString(),
        ];
    }
}
