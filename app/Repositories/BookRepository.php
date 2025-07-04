<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\BookNotFoundException;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Book>
     */
    public function getAllBooks(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $books = Book::with('publisher')
            ->paginate($perPage, ['*'], 'page', $page);

        return $books;
    }

    public function findBookById(int $id): ?Book
    {
        return Book::find($id) ?? null;
    }

    /**
     * @param array<string, string> $data
     */
    public function createBook(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * @param array<string, string> $data
     */
    public function updateBook(int $id, array $data): Book
    {
        $book = $this->findBookById($id);

        if (!$book) {
            throw new BookNotFoundException;
        }

        $book->update($data);

        return $book;
    }

    public function deleteBookById(int $id): ?bool
    {
        $book = $this->findBookById($id);

        if (!$book) {
            throw new BookNotFoundException;
        }

        return $book->delete();
    }
}
