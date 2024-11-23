<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

class PublisherRepository implements PublisherRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<Publisher>
     */
    public function getAllPublisher(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Publisher::paginate($perPage, ['*'], 'page', $page);
    }

    public function findPublisherById(int $id): ?Publisher
    {
        return Publisher::find($id) ?? null;
    }

    /**
     * @return LengthAwarePaginator<Book>
     *
     * @throws PublisherNotFoundException
     */
    public function findBooksByPublisherId(
        int $id,
        int $perPage = 10,
        int $page = 1
    ): LengthAwarePaginator
    {
        $publisher = Publisher::with('books')->find($id);

        if (!$publisher) {
            throw new PublisherNotFoundException;
        }

        if ($publisher->relationLoaded('books') && $publisher->books()->exists()) {
            return $publisher->books()->paginate($perPage, ['*'], 'page', $page);
        }

        return new LengthAwarePaginator([], 0, $perPage, $page);
    }

    /**
     * @param array<string, string> $data
     */
    public function createPublisher(array $data): Publisher
    {
        return Publisher::create($data);
    }

    /**
     * @param array<string, string> $data
     */
    public function updatePublisher(int $id, array $data): Publisher
    {
        $publisher = $this->findPublisherById($id);

        if (!$publisher) {
            throw new PublisherNotFoundException;
        }

        $publisher->update($data);

        return $publisher;
    }

    public function deletePublisherById(int $id): ?bool
    {
        $publisher = $this->findPublisherById($id);

        if (!$publisher) {
            throw new PublisherNotFoundException;
        }

        return $publisher->delete();
    }
}
