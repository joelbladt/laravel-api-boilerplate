<?php declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\PublisherNotDeletedException;
use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class PublisherRepository implements PublisherRepositoryInterface
{
    /**
     * @return Collection<int, Publisher>
     */
    public function all(): Collection
    {
        $publisher = Publisher::with('books')->get()->all();
        return Collection::make($publisher);
    }

    /**
     * @param array<string, string> $data
     * @return Publisher
     */
    public function create(array $data): Publisher
    {
        return Publisher::create($data);
    }

    /**
     * @param array<string, string> $data
     * @param int $id
     * @return Publisher
     */
    public function update(array $data, int $id): Publisher
    {
        $publisher = Publisher::find($id);

        if (!$publisher) {
            throw new PublisherNotFoundException();
        }

        $publisher->update($data);
        return $publisher;
    }

    /**
     * @param int $id
     * @return Publisher
     * @throws PublisherNotFoundException
     */
    public function show(int $id): Publisher
    {
        $publisher = Publisher::find($id);

        if (!$publisher) {
            throw new PublisherNotFoundException();
        }

        return $publisher;
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws PublisherNotDeletedException
     */
    public function delete(int $id): JsonResponse
    {
        $book = Publisher::find($id);

        if (!$book || !$book->delete()) {
            throw new PublisherNotDeletedException();
        }

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
