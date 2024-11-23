<?php

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BookResource",
 *     type="object",
 *     title="Book Resource",
 *     description="Scheme for a Book",
 *
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Book title",
 *         example="Harry Potter and the Order of the Phoenix"
 *     ),
 *     @OA\Property(
 *          property="author",
 *          type="string",
 *          description="Author of the Book",
 *          example="Joanne K. Rowling"
 *     ),
 *     @OA\Property(
 *          property="isbn",
 *          type="string",
 *          description="ISBN of the Book",
 *          example="9780747551003"
 *     ),
 *     @OA\Property(
 *          property="published_year",
 *          type="integer",
 *          description="Year of publication",
 *          example=2003
 *      ),
 *     @OA\Property(
 *         property="publisher",
 *         ref="#/components/schemas/PublisherResource",
 *         description="Publisher who published the book",
 *     ),
 *     @OA\Property(
 *          property="genres",
 *          type="string",
 *          description="Categories of the Book",
 *          example="Fantasy, Adventure"
 *     ),
 *     @OA\Property(
 *          property="summary",
 *          type="string",
 *          description="Summary of the book",
 *          example="Dumbledore lowered his hands and surveyed Harry through his half-moon glasses. 'It is time,' he said, 'for me to tell you what I should have told you five years ago, Harry. Please sit down. I am going to tell you everything.' Harry Potter is due to start his fifth year at Hogwarts School of Witchcraft and Wizadry. He is desperate to get back to school and find out why hiss friends Ron and Hermione have been so secretive all summer. However, what Harry is about to discover in his new year at Hogwarts will turn his whole world upside down... But before he even gets to school, Harry has an unexpected and frightening encounter with two Dementors, has to face a court hearing at the Ministry of Magic and has been escorted on a night-time broomstick ride to the secret headquarters of a mysterious group called 'The Order of the Phoenix'. And that is just the start. A gripping and electrifying novel, full of suspense, secrets, and - of course - magic."
 *      ),
 * )
 *
 * @mixin Book
 */
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'publisher' => $this->whenLoaded('publisher', fn() => new PublisherResource($this->publisher)),
            'publication_year' => $this->publication_year,
            'genres' => $this->genres,
            'summary' => $this->summary,
        ];

        if (!$this->relationLoaded('publisher')) {
            unset($data['publisher']);
        }

        return $data;
    }
}
