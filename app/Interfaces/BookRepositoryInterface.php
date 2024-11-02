<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface BookRepositoryInterface
{
    /**
     * @return Collection<int, Book>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     * @return Book
     */
    public function create(array $data): Book;

    /**
     * @param int $id
     * @return Book
     */
    public function show(int $id): Book;

    /**
     * @param array<string, mixed> $data
     * @param int $id
     * @return Book
     */
    public function update(array $data, int $id): Book;

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse;
}
