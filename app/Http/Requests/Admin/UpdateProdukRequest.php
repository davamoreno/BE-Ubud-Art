<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'         => 'sometimes|string|max:255',
            'deskripsi'     => 'sometimes|string',
            'detail'        => 'sometimes|string|max:100',
            'image'         => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'toko_id'       => 'sometimes|exists:tokos,id',
            'kategori_id'   => 'sometimes|exists:kategoris,id',
            'tag_id'        => 'sometimes|exists:tags,id'
        ];
    }
}
