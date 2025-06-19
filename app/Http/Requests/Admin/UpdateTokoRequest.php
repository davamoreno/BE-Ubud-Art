<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTokoRequest extends FormRequest
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
            'nama'          => 'sometimes|string|max:50',
            'deskripsi'     => 'sometimes|string|max:255',
            'telepon'       => 'sometimes|string|max:25',
            'image'         => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link'          => 'nullable|url',
            'status'        => 'sometimes|in:active,inactive',
        ];
    }
}
