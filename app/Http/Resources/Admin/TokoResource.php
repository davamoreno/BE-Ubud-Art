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
            'lantai' => $this->lantai ?? null,
            'nomor_toko' => $this->nomor_toko ?? null,
            'telepon' => $this->telepon ?? null,
            'link' => $this->link ?? null,
            'status' => $this->status ?? null,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
