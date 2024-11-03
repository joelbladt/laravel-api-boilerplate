<?php declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\PublisherNotDeletedException;
use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;

class PublisherRepository implements PublisherRepositoryInterface
{
    /**
     * @return Collection<int, Publisher>
     */
    public function getAllPublisher(): Collection
    {
        $publisher = Publisher::with('books')->get()->all();
        return Collection::make($publisher);
    }

    /**
     * @param int $id
     * @return Publisher|null
     */
    public function findPublisherById(int $id): ?Publisher
    {
        return Publisher::find($id) ?? null;
    }

    /**
     * @param array<string, string> $data
     * @return Publisher
     */
    public function createPublisher(array $data): Publisher
    {
        return Publisher::create($data);
    }

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Publisher
     */
    public function updatePublisher(int $id, array $data): Publisher
    {
        $publisher = $this->findPublisherById($id);

        if (!$publisher) {
            throw new PublisherNotFoundException();
        }

        $publisher->update($data);

        return $publisher;
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function deletePublisherById(int $id): ?bool
    {
        $publisher = $this->findPublisherById($id);

        if (!$publisher) {
            return false;
        }

        return $publisher->delete();
    }
}
