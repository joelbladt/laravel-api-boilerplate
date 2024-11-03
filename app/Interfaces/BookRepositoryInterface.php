<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface
{
    /**
     * @return Collection<int, Book>
     */
    public function getAllBooks(): Collection;

    /**
     * @param int $id
     * @return Book
     */
    public function findBookById(int $id): ?Book;

    /**
     * @param array<string, mixed> $data
     * @return Book
     */
    public function createBook(array $data): Book;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Book
     */
    public function updateBook(int $id, array $data): Book;

    /**
     * @param int $id
     * @return bool|null
     */
    public function deleteBookById(int $id): ?bool;
}
