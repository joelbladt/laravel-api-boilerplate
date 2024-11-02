<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => [
                'required',
                'string',
                Rule::unique('books')
                    ->where(function ($query) {
                        return $query->where('isbn', $this->isbn);
                    }),
            ],
            'publisher_id' => 'integer|nullable',
            'publication_year' => 'integer|nullable',
            'genres' => 'string|nullable',
            'summary' => 'string|nullable',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'isbn.unique' => 'The Book has already been taken.',
        ];
    }
}
