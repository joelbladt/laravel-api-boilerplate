<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\BookNotFoundException;
use App\Exceptions\PublisherNotFoundException;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\PublisherRepositoryInterface;
use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepository,
        protected PublisherRepositoryInterface $publisherRepository,
    )
    {
    }

    /**
     * @return LengthAwarePaginator<Book>
     */
    public function getAllBooks(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->bookRepository->getAllBooks($perPage, $page);
    }

    /**
     * @throws BookNotFoundException
     */
    public function findBookById(int $id): ?Book
    {
        $book = $this->bookRepository->findBookById($id);

        if (!$book) {
            throw new BookNotFoundException;
        }

        if (isset($book->publisher_id)) {
            $book->load('publisher');
        }

        return $book;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws PublisherNotFoundException
     */
    public function createBook(array $data): Book
    {
        if (isset($data['publisher_id'])) {
            if (!is_int($data['publisher_id'])) {
                throw new InvalidArgumentException('The publisher_id must be an integer.');
            }

            $publisher = $this->publisherRepository->findPublisherById($data['publisher_id']);
            if (!$publisher) {
                throw new PublisherNotFoundException;
            }
        }

        return $this->bookRepository->createBook($data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws PublisherNotFoundException
     */
    public function updateBook(int $id, array $data): Book
    {
        if (isset($data['publisher_id'])) {
            if (!is_int($data['publisher_id'])) {
                throw new InvalidArgumentException('The publisher_id must be an integer.');
            }

            $publisher = $this->publisherRepository->findPublisherById($data['publisher_id']);
            if (!$publisher) {
                throw new PublisherNotFoundException;
            }
        }

        return $this->bookRepository->updateBook($id, $data);
    }

    public function deleteBook(int $id): ?bool
    {
        return $this->bookRepository->deleteBookById($id);
    }
}
