<?php

namespace Tests\Unit;

use App\Exceptions\PublisherNotFoundException;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class PublisherServiceTest extends TestCase
{
    use RefreshDatabase, withFaker;

    protected PublisherService $publisherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publisherService = app(PublisherService::class);
    }

    public function test_can_create_a_publisher(): void
    {
        $data = [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];

        $publisher = $this->publisherService->createPublisher($data);

        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertEquals($data['name'], $publisher->name);
        $this->assertEquals($data['email'], $publisher->email);
        $this->assertEquals($data['website'], $publisher->website);
    }

    public function test_it_can_update_a_publisher(): void
    {
        $data = [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];

        $updatedData = [
            'phone' => $this->faker->phoneNumber(),
        ];

        $createPublisher = $this->publisherService->createPublisher($data);
        $publisher = $this->publisherService->updatePublisher($createPublisher->id, $updatedData);

        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertEquals($data['name'], $publisher->name);
        $this->assertEquals($data['email'], $publisher->email);
        $this->assertEquals($data['website'], $publisher->website);
        $this->assertEquals($updatedData['phone'], $publisher->phone);
    }

    public function test_it_can_not_update_a_publisher(): void
    {
        try {
            $this->publisherService->updatePublisher(999, [
                'phone' => $this->faker->phoneNumber(),
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

    public function test_it_can_show_a_publisher(): void
    {
        $data = [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];

        $createPublisher = $this->publisherService->createPublisher($data);
        $publisher = $this->publisherService->findPublisherById($createPublisher->id);

        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertEquals($data['name'], $publisher->name);
        $this->assertEquals($data['email'], $publisher->email);
        $this->assertEquals($data['website'], $publisher->website);
        $this->assertNull($publisher->address);
        $this->assertNull($publisher->zipcode);
        $this->assertNull($publisher->city);
        $this->assertNull($publisher->country);
        $this->assertNull($publisher->phone);
    }

    public function test_it_can_delete_a_publisher(): void
    {
        $data = [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];

        $createPublisher = $this->publisherService->createPublisher($data);
        $publisher = $this->publisherService->deletePublisher($createPublisher->id);

        $this->assertTrue($publisher);
        $this->assertDatabaseMissing('publishers', ['id' => $createPublisher->id]);
    }
}
