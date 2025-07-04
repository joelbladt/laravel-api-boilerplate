<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateBookRequest",
 *     type="object",
 *     title="Update Book Request",
 *     description="RequestBody to update a Book",
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
 *         description="Summary of the Book",
 *         nullable=true
 *     )
 * )
 */
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
            'title' => 'sometimes|required|string|filled',
            'author' => 'sometimes|required|string|filled',
            'isbn' => [
                'sometimes',
                'required',
                'string',
                'filled',
                Rule::unique('books')
                    ->ignore($this->books)
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
}
