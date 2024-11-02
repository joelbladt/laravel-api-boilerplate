<?php declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\BookNotDeletedException;
use App\Exceptions\BookNotFoundException;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class BookRepository implements BookRepositoryInterface
{
    /**
     * @return Collection<int, Book>
     */
    public function all(): Collection
    {
        $books = Book::with('publisher')->get()->all();
        return Collection::make($books);
    }

    /**
     * @param array<string, string> $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * @param array<string, string> $data
     * @param int $id
     * @return Book
     */
    public function update(array $data, int $id): Book
    {
        $book = Book::find($id);

        if (!$book) {
            throw new BookNotFoundException();
        }

        if ($book->update($data) && !empty($data['publisher_id'])) {
            $book->load('publisher');
        }

        return $book;
    }

    /**
     * @param int $id
     * @return Book
     * @throws BookNotFoundException
     */
    public function show(int $id): Book
    {
        $book = Book::find($id);

        if (!$book) {
            throw new BookNotFoundException();
        }

        $book->load('publisher');

        return $book;
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws BookNotDeletedException
     */
    public function delete(int $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book || !$book->delete()) {
            throw new BookNotDeletedException();
        }

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
