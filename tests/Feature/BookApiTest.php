<?php

namespace Tests\Feature;

use App\Exceptions\PublisherNotFoundException;
use App\Http\Resources\BookResourceCollection;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_all_books(): void
    {
        $books = Book::factory()
            ->count(10)
            ->forPublisher()
            ->create();

        $response = $this->get('/api/books');
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'author',
                        'isbn',
                        'publisher' => [
                            'name',
                            'address',
                            'zipcode',
                            'city',
                            'country',
                            'website',
                            'email',
                            'phone',
                        ],
                        'publication_year',
                        'genres',
                        'summary',
                    ],
                ],
                'meta' => [
                    'total'
                ],
            ]);

        $resourceCollection = new BookResourceCollection($books->load('publisher'));
        $this->assertEquals($resourceCollection
            ->response()
            ->getData(true),
            $response->json()
        );
    }

    public function test_create_book(): void
    {
        $book = Book::factory()->make();
        $response = $this->postJson('/api/books', [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
        ])
            ->decodeResponseJson();

        $this->assertEquals($book->title, $response['title']);
        $this->assertEquals($book->author, $response['author']);
        $this->assertEquals($book->isbn, $response['isbn']);
        $this->assertNotEmpty($response['created_at']);

        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'created_at' => now(),
        ]);
    }

    public function test_create_book_with_publisher(): void
    {
        $publisher = Publisher::factory()->create();
        $book = Book::factory()->make();

        $response = $this->postJson('/api/books', [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'publisher_id' => $publisher->id,
        ])
            ->decodeResponseJson();

        $this->assertEquals($book->title, $response['title']);
        $this->assertEquals($book->author, $response['author']);
        $this->assertEquals($book->isbn, $response['isbn']);
        $this->assertNotEmpty($response['created_at']);

        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
        ]);
    }

    public function test_create_book_handle_publisher_not_found_exception(): void
    {
        try {
            $book = Book::factory()->make();

            $response = $this->postJson('/api/books', [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'publisher_id' => 999,
            ])
                ->decodeResponseJson();

            $this->assertDatabaseMissing(Book::class, [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
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

    public function test_create_book_handle_invalid_argument_exception(): void
    {
        try {
            $book = Book::factory()->make();

            $response = $this->postJson('/api/books', [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'publisher_id' => '999',
            ])
                ->decodeResponseJson();

            $this->assertDatabaseMissing(Book::class, [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
            ]);
        } catch (InvalidArgumentException $e) {
            $response = $e->render(request());

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals(404, $response->getStatusCode());

            $this->assertEquals([
                'error' => [
                    'message' => "The publisher_id must be an integer."
                ]
            ], $response->getData(true));
        }
    }

    public function test_create_book_already_exists(): void
    {
        $attributes = [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
        ];

        Book::factory()->create($attributes);

        $book = Book::factory()->make($attributes);
        $response = $this->postJson('/api/books', [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
        ])
            ->decodeResponseJson();

        $this->assertEquals('The Book has already been taken.', $response['message']);
        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
        ]);
    }

    public function test_show_book(): void
    {
        Book::factory()
            ->forPublisher()
            ->create();

        $response = $this->get('/api/books/1');
        $response->assertStatus(200)
            ->assertJsonCount(9)
            ->assertJsonStructure([
                'title',
                'author',
                'isbn',
                'publisher' => [
                    'name',
                    'address',
                    'zipcode',
                    'city',
                    'country',
                    'website',
                    'email',
                    'phone',
                ],
                'publication_year',
                'genres',
                'summary',
                'created_at',
                'updated_at',
            ]);

        $result = $response->decodeResponseJson();
        $this->assertDatabaseHas(Book::class, [
            'title' => $result['title'],
            'author' => $result['author'],
            'isbn' => $result['isbn'],
            'publication_year' => $result['publication_year'],
            'genres' => $result['genres'],
            'summary' => $result['summary'],
        ]);
    }

    public function test_show_book_handle_not_found_exception(): void
    {
        $response = $this->get('/api/books/999');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => 'Book can not found'
                ]
            ]);

        $this->assertDatabaseMissing(Book::class, [
            'id' => 999,
        ]);
    }

    public function test_update_book(): void
    {
        $book = Book::factory()->create();
        $data = [
            'summary' => $this->faker->paragraph(),
        ];

        $response = $this->putJson('/api/books/' . $book->id, $data);
        $response->assertOk()
            ->assertJsonStructure([
                'title',
                'author',
                'isbn',
                'publication_year',
                'genres',
                'summary',
            ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($book->title, $result['title']);
        $this->assertEquals($book->isbn, $result['isbn']);
        $this->assertEquals($data['summary'], $result['summary']);
        $this->assertDatabaseHas(Book::class, [
            'title' => $result['title'],
            'author' => $result['author'],
            'isbn' => $result['isbn'],
            'publication_year' => $result['publication_year'],
            'genres' => $result['genres'],
            'summary' => $result['summary'],
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function test_update_book_handle_not_found_exception(): void
    {
        $response = $this->putJson('/api/books/999', [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->isbn13(),
        ]);

        $response->assertNotFound()
            ->assertJsonStructure([
                'error' => [
                    'message',
                ]
            ]);

        /** @var array<string, string> $result */
        $result = $response->decodeResponseJson();

        $this->assertEquals('Book can not found', $result['error']['message']);
        $this->assertDatabaseMissing(Book::class, [
            'id' => 999,
        ]);
    }

    public function test_update_book_handle_publisher_not_found_exception(): void
    {
        try {
            $book = Book::factory()->create();

            $response = $this->putJson('/api/books/' . $book->id, [
                'title' => $this->faker->sentence(3),
                'publisher_id' => 999,
            ])
                ->decodeResponseJson();

            $this->assertDatabaseMissing(Book::class, [
                'publisher_id' => 999,
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

    public function test_update_book_handle_invalid_argument_exception(): void
    {
        try {
            $book = Book::factory()->create();

            $response = $this->postJson('/api/books/' . $book->id, [
                'publisher_id' => '999',
            ])
                ->decodeResponseJson();

            $this->assertDatabaseMissing(Book::class, [
                'id' => $book->id,
                'publisher_id' => 999,
            ]);
        } catch (InvalidArgumentException $e) {
            $response = $e->render(request());

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals(404, $response->getStatusCode());

            $this->assertEquals([
                'error' => [
                    'message' => "The publisher_id must be an integer."
                ]
            ], $response->getData(true));
        }
    }

    public function test_delete_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->delete('/api/books/' . $book->id);
        $response->assertNoContent();

        $this->assertDatabaseMissing(Book::class, [
            'id' => $book->id,
        ]);
    }

    public function test_delete_book_failed(): void
    {
        $response = $this->delete('/api/books/999');
        $response->assertNoContent();

        $this->assertDatabaseMissing(Book::class, [
            'id' => 999,
        ]);
    }
}
