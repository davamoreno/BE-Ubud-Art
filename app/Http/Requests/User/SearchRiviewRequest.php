<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SearchRiviewRequest extends FormRequest
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
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => [
                'nullable',
                'string',
                Rule::in(['newest', 'oldest', 'highest', 'lowest']),
            ],

            'ratings' => [
                'nullable',
                'array',
            ],

            'ratings.*' => [
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],
        ];
    }
}
