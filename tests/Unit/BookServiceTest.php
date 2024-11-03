<?php

namespace Tests\Unit;

use App\Exceptions\BookNotFoundException;
use App\Exceptions\PublisherNotFoundException;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase, withFaker;

    protected BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = app(BookService::class);
    }

    /**
     * @return void
     * @throws PublisherNotFoundException
     */
    public function test_can_create_a_book(): void
    {
        $data = [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
        ];

        $book = $this->bookService->createBook($data);

        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals($data['title'], $book->title);
        $this->assertEquals($data['author'], $book->author);
        $this->assertEquals($data['isbn'], $book->isbn);
    }

    /**
     * @throws PublisherNotFoundException
     */
    public function test_can_update_a_book(): void
    {
        $data = [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
        ];

        $updatedData = [
            'summary' => $this->faker->paragraph(),
        ];

        $createBook = $this->bookService->createBook($data);
        $book = $this->bookService->updateBook($createBook->id, $updatedData);

        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'summary' => $book->summary,
        ]);
    }

    public function test_can_not_update_a_book_bacause_not_found(): void
    {
        try {
            $book = $this->bookService->updateBook(999, [
                'title' => $this->faker->sentence(3),
            ]);
        } catch (BookNotFoundException $e) {
            $response = $e->render(request());

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals(404, $response->getStatusCode());

            $this->assertEquals([
                'error' => [
                    'message' => "Book can not found"
                ]
            ], $response->getData(true));
        }
    }

    /**
     * @return void
     */
    public function test_can_not_update_a_book_handle_publisher_not_found_exception(): void
    {
        try {
            $book = $this->bookService->updateBook(999, [
                'publisher_id' => 1,
            ]);
        } catch (PublisherNotFoundException $e) {
            $response = $e->render(request());

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals(404, $response->getStatusCode());

            $this->assertEquals([
                'error' => [
                    'message' => "Publisher can not found"
                ]
            ], $response->getData(true));
        }
    }

    /**
     * @return void
     * @throws PublisherNotFoundException
     */
    public function test_can_not_update_a_book_handle_invalid_argument_exception(): void
    {
        try {
            $data = [
                'title' => $this->faker->sentence(3),
                'author' => $this->faker->name(),
                'isbn' => $this->faker->isbn13(),
            ];

            $newBook = $this->bookService->createBook($data);
            $book = $this->bookService->updateBook($newBook->id, [
                'publisher_id' => '1',
            ]);

            $this->assertFalse($book);
        } catch (InvalidArgumentException $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals(0, $e->getCode());
            $this->assertEquals('The publisher_id must be an integer.', $e->getMessage());
        }
    }

    /**
     * @return void
     * @throws BookNotFoundException
     * @throws PublisherNotFoundException
     */
    public function test_can_show_a_book(): void
    {
        $data = [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
        ];

        $createBook = $this->bookService->createBook($data);
        $book = $this->bookService->findBookById($createBook->id);

        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals($data['title'], $book->title);
        $this->assertEquals($data['author'], $book->author);
        $this->assertEquals($data['isbn'], $book->isbn);
        $this->assertNull($book->publisher_id);
        $this->assertNull($book->publication_year);
        $this->assertNull($book->genres);
        $this->assertNull($book->summary);
    }

    /**
     * @return void
     * @throws PublisherNotFoundException
     */
    public function test_can_delete_a_book(): void
    {
        $data = [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
        ];

        $createBook = $this->bookService->createBook($data);
        $book = $this->bookService->deleteBook($createBook->id);

        $this->assertTrue($book);
        $this->assertDatabaseMissing('books', ['id' => $createBook->id]);
    }
}
