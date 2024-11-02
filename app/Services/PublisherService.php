<?php declare(strict_types=1);

namespace App\Services;

use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

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
    public function all(): Collection
    {
        return $this->publisherRepository->all();
    }

    /**
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function create(array $data): Publisher
    {
        return $this->publisherRepository->create($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function update(int $id, array $data): Publisher
    {
        return $this->publisherRepository->update($data, $id);
    }

    /**
     * @param int $id
     * @return Publisher
     */
    public function show(int $id): Publisher
    {
        return $this->publisherRepository->show($id);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        return $this->publisherRepository->delete($id);
    }
}
