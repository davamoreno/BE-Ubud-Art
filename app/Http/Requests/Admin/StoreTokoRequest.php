<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTokoRequest extends FormRequest
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
            'nama'          => 'required|string|max:50',
            'lantai'        => 'required|string|max:10',
            'telepon'       => 'required|string|max:25',
            'nomor_toko'    => 'required|string|max:50',
            'link'          => 'nullable|url',
            'status'        => 'sometimes|in:active,inactive',
        ];
    }
}
