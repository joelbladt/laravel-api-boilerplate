<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<Book>
     */
    public function getAllBooks(int $perPage = 10, int $page = 1): LengthAwarePaginator;

    public function findBookById(int $id): ?Book;

    /**
     * @param int $publisherId
     * @return Book|null
     */
    //public function findBooksByPublisherId(int $publisherId): ?Book;

    /**
     * @param array<string, mixed> $data
     */
    public function createBook(array $data): Book;

    /**
     * @param array<string, mixed> $data
     */
    public function updateBook(int $id, array $data): Book;

    public function deleteBookById(int $id): ?bool;
}
