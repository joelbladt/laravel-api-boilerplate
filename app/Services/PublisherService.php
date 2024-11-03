<?php declare(strict_types=1);

namespace App\Services;

use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;

class PublisherService
{
    public function __construct(
        protected PublisherRepositoryInterface $publisherRepository
    )
    {
    }

    /**
     * @return Collection<int, Publisher>
     */
    public function getAllPublisher(): Collection
    {
        return $this->publisherRepository->getAllPublisher();
    }

    /**
     * @param int $id
     * @return Publisher|null
     * @throws PublisherNotFoundException
     */
    public function findPublisherById(int $id): ?Publisher
    {
        $publisher = $this->publisherRepository->findPublisherById($id);

        if (!$publisher) {
            throw new PublisherNotFoundException();
        }

        return $publisher;
    }

    /**
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function createPublisher(array $data): Publisher
    {
        return $this->publisherRepository->createPublisher($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function updatePublisher(int $id, array $data): Publisher
    {
        return $this->publisherRepository->updatePublisher($id, $data);
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function deletePublisher(int $id): ?bool
    {
        return $this->publisherRepository->deletePublisherById($id);
    }
}
