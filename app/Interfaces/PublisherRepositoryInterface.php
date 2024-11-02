<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface PublisherRepositoryInterface
{
    /**
     * @return Collection<int, Publisher>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     * @return Publisher
     */
    public function create(array $data): Publisher;

    /**
     * @param int $id
     * @return Publisher
     */
    public function show(int $id): Publisher;

    /**
     * @param array<string, mixed> $data
     * @param int $id
     * @return Publisher
     */
    public function update(array $data, int $id): Publisher;

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse;
}
