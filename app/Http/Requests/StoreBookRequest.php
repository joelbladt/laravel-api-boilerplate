<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreBookRequest",
 *     type="object",
 *     title="Store Book Request",
 *     description="RequestBody to create a Book",
 *     required={"title", "author", "isbn"},
 *
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Book title",
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="Author of the Book",
 *     ),
 *     @OA\Property(
 *         property="isbn",
 *         type="string",
 *         description="ISBN of the Book",
 *     ),
 *     @OA\Property(
 *         property="published_year",
 *         type="integer",
 *         description="Year of publication",
 *         example=1900,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="published_id",
 *         type="integer",
 *         description="Id of the Publisher",
 *         example=1,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="genres",
 *         type="string",
 *         description="Categories of the Book",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="summary",
 *         type="string",
 *         description="Summary of the book",
 *         nullable=true
 *     )
 * )
 */
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
            'title' => 'required|string|filled',
            'author' => 'required|string|filled',
            'isbn' => [
                'required',
                'string',
                'filled',
                Rule::unique('books')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        $query->where('isbn', $this->isbn);
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
