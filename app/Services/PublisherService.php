<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

class PublisherService
{
    public function __construct(
        protected PublisherRepositoryInterface $publisherRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<int, Publisher>
     */
    public function getAllPublisher(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->publisherRepository->getAllPublisher();
    }

    /**
     * @throws PublisherNotFoundException
     */
    public function findPublisherById(int $id): ?Publisher
    {
        $publisher = $this->publisherRepository->findPublisherById($id);

        if (!$publisher) {
            throw new PublisherNotFoundException;
        }

        return $publisher;
    }

    /**
     * @return LengthAwarePaginator<int, Book>
     */
    public function findBooksByPublisherId(
        int $publisherId,
        int $perPage = 10,
        int $page = 1,
    ): LengthAwarePaginator
    {
        return $this->publisherRepository->findBooksByPublisherId($publisherId, $perPage, $page);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPublisher(array $data): Publisher
    {
        return $this->publisherRepository->createPublisher($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updatePublisher(int $id, array $data): Publisher
    {
        return $this->publisherRepository->updatePublisher($id, $data);
    }

    public function deletePublisher(int $id): ?bool
    {
        return $this->publisherRepository->deletePublisherById($id);
    }
}
