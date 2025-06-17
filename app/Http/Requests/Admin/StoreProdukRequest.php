<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
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
            'title'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'detail'    => 'required|string|max:100',
            'image'     => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'toko_id'   => 'required|exists:tokos,id',
        ];
    }
}
