<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

interface PublisherRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Publisher>
     */
    public function getAllPublisher(): LengthAwarePaginator;

    public function findPublisherById(int $id): ?Publisher;

    /**
     * @return LengthAwarePaginator<int, Book>
     */
    public function findBooksByPublisherId(
        int $id,
        int $perPage = 10,
        int $page = 1
    ): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function createPublisher(array $data): Publisher;

    /**
     * @param array<string, mixed> $data
     */
    public function updatePublisher(int $id, array $data): Publisher;

    public function deletePublisherById(int $id): ?bool;
}
