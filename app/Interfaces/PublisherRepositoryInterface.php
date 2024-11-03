<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;

interface PublisherRepositoryInterface
{
    /**
     * @return Collection<int, Publisher>
     */
    public function getAllPublisher(): Collection;

    /**
     * @param int $id
     * @return Publisher|null
     */
    public function findPublisherById(int $id): ?Publisher;

    /**
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function createPublisher(array $data): Publisher;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function updatePublisher(int $id, array $data): Publisher;

    /**
     * @param int $id
     * @return bool|null
     */
    public function deletePublisherById(int $id): ?bool;
}
