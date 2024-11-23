<?php

namespace Tests\Feature;

use App\Http\Resources\PublisherResourceCollection;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PublisherApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_publisher(): void
    {
        $publisher = Publisher::factory()
            ->count(10)
            ->create();

        $response = $this->get('/api/publisher');
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'email',
                        'website',
                        'address',
                        'zipcode',
                        'city',
                        'country',
                        'phone',
                    ],
                ],
                'meta' => [
                    'per_page',
                    'current_page',
                    'last_page',
                    'total',
                ],
            ]);

        $resourceCollection = new PublisherResourceCollection(
            new LengthAwarePaginator($publisher, 10, 10)
        );

        $this->assertEquals($resourceCollection
            ->response()
            ->getData(true),
            $response->json()
        );
    }

    public function test_create_publisher(): void
    {
        $publisher = Publisher::factory()->make();
        $response = $this->postJson('/api/publisher', [
            'name' => $publisher->name,
            'email' => $publisher->email,
            'website' => $publisher->website,
            'address' => $publisher->address,
            'zipcode' => $publisher->zipcode,
            'city' => $publisher->city,
            'country' => $publisher->country,
            'phone' => $publisher->phone,
        ])
            ->decodeResponseJson();

        $this->assertEquals($publisher->name, $response['name']);
        $this->assertEquals($publisher->email, $response['email']);
        $this->assertEquals($publisher->website, $response['website']);
        $this->assertEquals($publisher->address, $response['address']);
        $this->assertEquals($publisher->zipcode, $response['zipcode']);
        $this->assertEquals($publisher->city, $response['city']);
        $this->assertEquals($publisher->country, $response['country']);
        $this->assertEquals($publisher->phone, $response['phone']);

        $this->assertDatabaseHas(Publisher::class, [
            'name' => $publisher->name,
            'email' => $publisher->email,
            'website' => $publisher->website,
            'address' => $publisher->address,
            'zipcode' => $publisher->zipcode,
            'city' => $publisher->city,
            'country' => $publisher->country,
            'phone' => $publisher->phone,
        ]);
    }

    public function test_create_publisher_already_exists(): void
    {
        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'website' => $this->faker->url,
        ];

        Publisher::factory()->create($attributes);

        $publisher = Publisher::factory()->make($attributes);
        $response = $this->postJson('/api/publisher', [
            'name' => $publisher->name,
            'email' => $publisher->email,
            'website' => $publisher->website,
        ])
            ->decodeResponseJson();

        $this->assertEquals('The Publisher has already been taken.', $response['message']);
        $this->assertDatabaseHas(Publisher::class, [
            'name' => $publisher->name,
            'email' => $publisher->email,
            'website' => $publisher->website,
        ]);
    }

    public function test_show_publisher(): void
    {
        Publisher::factory()->create();

        $response = $this->get('/api/publisher/1');
        $response->assertStatus(200)
            ->assertJsonCount(8)
            ->assertJsonStructure([
                'name',
                'email',
                'website',
                'address',
                'zipcode',
                'city',
                'country',
                'phone',
            ]);

        $result = $response->decodeResponseJson();
        $this->assertDatabaseHas(Publisher::class, [
            'name' => $result['name'],
            'email' => $result['email'],
            'website' => $result['website'],
            'address' => $result['address'],
            'zipcode' => $result['zipcode'],
            'city' => $result['city'],
            'country' => $result['country'],
            'phone' => $result['phone'],
        ]);
    }

    public function test_show_books_from_publisher(): void
    {
        Book::factory()
            ->count(10)
            ->create([
                'publisher_id' => Publisher::factory()->create()->id,
            ]);

        $response = $this->get('/api/publisher/1/books');
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'author',
                        'isbn',
                        'publication_year',
                        'genres',
                        'summary',
                    ],
                ],
                'meta' => [
                    'per_page',
                    'current_page',
                    'last_page',
                    'total',
                ],
            ]);
    }

    public function test_show_null_books_from_publisher(): void
    {
        Publisher::factory()->create();

        $response = $this->get('/api/publisher/1/books');
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonStructure([
                'data' => [
                ],
                'meta' => [
                    'per_page',
                    'current_page',
                    'last_page',
                    'total',
                ],
            ]);
    }

    public function test_show_books_from_publisher_handle_not_found_exception(): void
    {
        $response = $this->get('/api/publisher/999/books');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => 'Publisher can not found',
                ],
            ]);

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }

    public function test_show_publisher_handle_not_found_exception(): void
    {
        $response = $this->get('/api/publisher/999');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => 'Publisher can not found',
                ],
            ]);

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }

    public function test_update_publisher(): void
    {
        $publisher = Publisher::factory()->create();
        $data = [
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];

        $response = $this->putJson('/api/publisher/' . $publisher->id, $data);
        $response->assertOk()
            ->assertJsonStructure([
                'name',
                'email',
                'website',
                'address',
                'zipcode',
                'city',
                'country',
                'phone',
            ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($data['email'], $result['email']);
        $this->assertEquals($data['website'], $result['website']);
        $this->assertDatabaseHas(Publisher::class, [
            'id' => $publisher->id,
            'email' => $result['email'],
            'website' => $result['website'],
        ]);
    }

    public function test_update_publisher_handle_not_found_exception(): void
    {
        $response = $this->putJson('/api/publisher/999', [
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ]);

        $response->assertNotFound()
            ->assertJsonStructure([
                'error' => [
                    'message',
                ],
            ]);

        /** @var array<string, string> $result */
        $result = $response->decodeResponseJson();

        $this->assertEquals('Publisher can not found', $result['error']['message']);
        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }

    public function test_update_publisher_validation_unique(): void
    {
        $publisher = Publisher::factory()->count(2)->create();

        /** @var array<int, array<string, string>> $data */
        $data = $publisher->toArray();

        $response = $this->putJson('/api/publisher/2', [
            'name' => $data[0]['name'],
            'email' => $data[0]['email'],
            'website' => $data[0]['website'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_delete_publisher(): void
    {
        $publisher = Publisher::factory()->create();

        $response = $this->delete('/api/publisher/' . $publisher->id);
        $response->assertNoContent();

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => $publisher->id,
        ]);
    }

    public function test_delete_publisher_handle_exception(): void
    {
        $response = $this->delete('/api/publisher/999');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => 'Publisher can not found',
                ],
            ]);

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }
}
