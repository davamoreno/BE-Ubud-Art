<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiviewRequest extends FormRequest
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
            'user_id'   => 'sometimes|exists:users,id',
            'produk_id' => 'sometimes|exists:produks,id',
            'rating'    => 'sometimes|integer|min:1|max:5',
            'komentar'  => 'sometimes|string',
        ];
    }
}
