<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => 'string',
            'author' => 'string',
            'isbn' => [
                'string',
                Rule::unique('books')
                    ->ignore($this->books)
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
}
