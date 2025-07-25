<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SearchProdukRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            
            // --- TAMBAHKAN VALIDASI BARU DI SINI ---

            // 'sort' harus berupa string dan hanya boleh salah satu dari nilai yang ditentukan.
            'sort' => [
                'nullable',
                'string',
                Rule::in(['newest', 'oldest', 'asc', 'desc', 'highest', 'lowest']),
            ],

            // 'tags' harus berupa array jika ada.
            'tags' => 'nullable|array',
            
            // Setiap item di dalam array 'tags' harus ada di dalam tabel 'tags'.
            'tags.*' => 'integer|exists:tags,id',

            'kategori' => [
                'nullable',
                'integer',
                Rule::exists('kategoris', 'id')
            ],
        ];
    }
}
