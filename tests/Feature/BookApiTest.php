<?php

namespace Tests\Feature;

use App\Http\Resources\BookResourceCollection;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_books(): void
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

        $this->assertEquals($book->title, $response['data']['title']);
        $this->assertEquals($book->author, $response['data']['author']);
        $this->assertEquals($book->isbn, $response['data']['isbn']);
        $this->assertNotEmpty($response['data']['created_at']);

        // Prüfe Datenbank
        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'created_at' => now(),
        ]);
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

        // Prüfe Datenbank
        $this->assertDatabaseHas(Book::class, [
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
        ]);
    }

    public function test_show_book(): void
    {
        Book::factory()->forPublisher()->create();

        $response = $this->get('/api/books/1');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure([
                'data' => [
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
            ]);

        // Prüfe Datenbank
        $result = $response->decodeResponseJson();
        $this->assertDatabaseHas(Book::class, [
            'title' => $result['data']['title'],
            'author' => $result['data']['author'],
            'isbn' => $result['data']['isbn'],
            'publication_year' => $result['data']['publication_year'],
            'genres' => $result['data']['genres'],
            'summary' => $result['data']['summary'],
        ]);
    }

    public function test_show_book_not_found(): void
    {
        $book = Book::factory()->forPublisher()->create();

        $response = $this->get('/api/books/999');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => 'Book can not found'
                ]
            ]);

        $this->assertDatabaseHas(Book::class, [
            'id' => $book->id,
        ]);

        $this->assertDatabaseMissing(Book::class, [
            'id' => 999,
        ]);
    }

    public function test_update_book(): void
    {
        $book = Book::factory()->create();
        $publisher = Publisher::factory()->create();
        $updatedBook = Book::factory()->make();

        $response = $this->putJson('/api/books/' . $book->id, [
            'title' => $updatedBook->title,
            'isbn' => $updatedBook->isbn,
            'publisher_id' => $publisher->id,
            'summary' => $updatedBook->summary,
        ]);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
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
                ]
            ]);

        // Konvertiere das Ergebnis in ein Array
        $result = $response->decodeResponseJson();

        $this->assertEquals($updatedBook->title, $result['data']['title']);
        $this->assertEquals($updatedBook->isbn, $result['data']['isbn']);
        $this->assertEquals($updatedBook->summary, $result['data']['summary']);

        // Prüfe Datenbank
        $this->assertDatabaseHas(Book::class, [
            'title' => $result['data']['title'],
            'author' => $result['data']['author'],
            'isbn' => $result['data']['isbn'],
            'publication_year' => $result['data']['publication_year'],
            'genres' => $result['data']['genres'],
            'summary' => $result['data']['summary'],
        ]);
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
        $book = Book::factory()->create();

        $response = $this->delete('/api/books/999');
        $response->assertNotFound()
            ->assertExactJson([
            'error' => [
                'message' => "Book can not deleted"
            ]
        ]);

        $this->assertDatabaseHas(Book::class, [
            'id' => $book->id,
        ]);

        $this->assertDatabaseMissing(Book::class, [
            'id' => 999,
        ]);
    }
}
