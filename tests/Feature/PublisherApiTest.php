<?php

namespace Tests\Feature;

use App\Http\Resources\PublisherResourceCollection;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            ]);

        $resourceCollection = new PublisherResourceCollection($publisher);
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

        $this->assertEquals($publisher->name, $response['data']['name']);
        $this->assertEquals($publisher->email, $response['data']['email']);
        $this->assertEquals($publisher->website, $response['data']['website']);
        $this->assertEquals($publisher->address, $response['data']['address']);
        $this->assertEquals($publisher->zipcode, $response['data']['zipcode']);
        $this->assertEquals($publisher->city, $response['data']['city']);
        $this->assertEquals($publisher->country, $response['data']['country']);
        $this->assertEquals($publisher->phone, $response['data']['phone']);
        $this->assertNotEmpty($response['data']['created_at']);

        // Prüfe Datenbank
        $this->assertDatabaseHas(Publisher::class, [
            'name' => $publisher->name,
            'email' => $publisher->email,
            'website' => $publisher->website,
            'address' => $publisher->address,
            'zipcode' => $publisher->zipcode,
            'city' => $publisher->city,
            'country' => $publisher->country,
            'phone' => $publisher->phone,
            'created_at' => now(),
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

        // Prüfe Datenbank
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
            ->assertJsonCount(1)
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'website',
                    'address',
                    'zipcode',
                    'city',
                    'country',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Prüfe Datenbank
        $result = $response->decodeResponseJson();
        $this->assertDatabaseHas(Publisher::class, [
            'name' => $result['data']['name'],
            'email' => $result['data']['email'],
            'website' => $result['data']['website'],
            'address' => $result['data']['address'],
            'zipcode' => $result['data']['zipcode'],
            'city' => $result['data']['city'],
            'country' => $result['data']['country'],
            'phone' => $result['data']['phone'],
            'created_at' => $result['data']['created_at'],
            'updated_at' => $result['data']['updated_at'],
        ]);
    }

    public function test_show_publisher_not_found(): void
    {
        $publisher = Publisher::factory()->create();

        $response = $this->get('/api/publisher/999');
        $response->assertNotFound()
            ->assertExactJson([
                'error' => [
                    'message' => "Publisher can not found"
                ]
            ]);

        $this->assertDatabaseHas(Publisher::class, [
            'id' => $publisher->id,
        ]);

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }

    public function test_update_publisher(): void
    {
        $publisher = Publisher::factory()->create();
        $updatedPublisher = Publisher::factory()->make();

        $response = $this->putJson('/api/publisher/' . $publisher->id, [
            'name' => $updatedPublisher->name,
            'email' => $updatedPublisher->email,
            'website' => $updatedPublisher->website,
            'phone' => $updatedPublisher->phone,
        ]);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'website',
                    'address',
                    'zipcode',
                    'city',
                    'country',
                    'phone',
                    'created_at',
                    'updated_at',
                ]
            ]);

        // Konvertiere das Ergebnis in ein Array
        $result = $response->decodeResponseJson();

        $this->assertEquals($updatedPublisher->name, $result['data']['name']);
        $this->assertEquals($updatedPublisher->email, $result['data']['email']);
        $this->assertEquals($updatedPublisher->website, $result['data']['website']);
        $this->assertEquals($updatedPublisher->phone, $result['data']['phone']);

        // Prüfe Datenbank
        $this->assertDatabaseHas(Publisher::class, [
            'name' => $result['data']['name'],
            'email' => $result['data']['email'],
            'website' => $result['data']['website'],
            'phone' => $result['data']['phone'],
        ]);
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

    public function test_delete_publisher_failed(): void
    {
        $publisher = Publisher::factory()->create();

        $response = $this->delete('/api/publisher/999');
        $response->assertNotFound()
            ->assertExactJson([
            'error' => [
                'message' => "Publisher can not deleted"
            ]
        ]);

        $this->assertDatabaseHas(Publisher::class, [
            'id' => $publisher->id,
        ]);

        $this->assertDatabaseMissing(Publisher::class, [
            'id' => 999,
        ]);
    }
}
