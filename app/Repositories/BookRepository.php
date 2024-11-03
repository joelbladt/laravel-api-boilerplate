<?php declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\BookNotDeletedException;
use App\Exceptions\BookNotFoundException;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookRepository implements BookRepositoryInterface
{
    /**
     * @return Collection<int, Book>
     */
    public function getAllBooks(): Collection
    {
        $books = Book::with('publisher')->get()->all();
        return Collection::make($books);
    }

    /**
     * @param int $id
     * @return Book|null
     */
    public function findBookById(int $id): ?Book
    {
        return Book::find($id) ?? null;
    }

    /**
     * @param array<string, string> $data
     * @return Book
     */
    public function createBook(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Book
     */
    public function updateBook(int $id, array $data): Book
    {
        $book = $this->findBookById($id);

        if (!$book) {
            throw new BookNotFoundException();
        }

        $book->update($data);

        return $book;
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function deleteBookById(int $id): ?bool
    {
        $book = $this->findBookById($id);

        if (!$book) {
            return false;
        }

        return $book->delete();
    }
}
