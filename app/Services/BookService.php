<?php declare(strict_types=1);

namespace App\Services;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepository
    )
    {
    }

    /**
     * @return Collection<int, Book>
     */
    public function all(): Collection
    {
        return $this->bookRepository->all();
    }

    /**
     * @param array<string, mixed> $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return $this->bookRepository->create($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Book
     */
    public function update(int $id, array $data): Book
    {
        return $this->bookRepository->update($data, $id);
    }

    /**
     * @param int $id
     * @return Book
     */
    public function show(int $id): Book
    {
        return $this->bookRepository->show($id);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        return $this->bookRepository->delete($id);
    }
}
