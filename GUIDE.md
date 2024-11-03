# Working with the Boilerplate

This boilerplate uses the Repository Pattern, which separates the data access logic (repositories) from the business logic (services). This approach promotes cleaner, more maintainable code by providing a consistent way to interact with your models.

Here’s a guide on setting up and using the Repository Pattern with the example of a `Post` model.

> **Note on Test Coverage**  
> Please be aware that this boilerplate project does not guarantee 100% test coverage at all times. However, by following the basic guidelines provided and reviewing the implemented examples (e.g., `Book` and `Publisher`), you should be able to maintain a high level of test coverage. These examples serve as a guide for structuring your own tests and can help you achieve a consistent testing strategy throughout the project.

---

## Table of contents
1. [Generate the Model and Base Structure](#step-1-Generate-the-Model-and-Base-Structure)
2. [Setting up the Repository Pattern](#step-2-setting-up-the-repository-pattern)
3. [Create the Repository Interface](#step-3-create-the-repository-interface)
4. [Create Custom Exceptions](#step-4-create-custom-exceptions)
5. [Create the Repository](#step-5-create-the-repository)
6. [Bind the Repository to the Interface](#step-6-bind-the-repository-to-the-interface)
7. [Create the Service](#step-7-create-the-service)
8. [Update the Controller](#step-8-update-the-controller)
9. [Create API Resources](#step-9-create-api-resources)
10. [The "Ugly" Part - Testing for High Coverage](#step-10-the-ugly-part---testing-for-high-coverage)
    1. [Setting Up the Testing Environment](#101-setting-up-the-testing-environment)
    2. [Writing Unit Tests for Services](#102-writing-unit-tests-for-services)
    3. [Writing Feature Tests for API Endpoints](#103-writing-feature-tests-for-api-endpoints)
    4. [Running the Tests](#104-running-the-tests)
11. [Summary](#summary)
    1. [Testing](#testing)

---

## Step 1: Generate the Model and Base Structure
First, create a new model and associated files with the following command:

```bash
php artisan make:model Post -a
```

This command creates:
- `app/Models/Post.php`: the `Post` model
- `database/migrations/*_create_posts_table.php`: migration file for the posts table
- `app/Http/Controllers/PostController.php`: the controller for handling `Post` requests
- `app/Policies/PostPolicy.php`: policy file for `Post` authorization (if applicable)

## Step 2: Setting up the Repository Pattern
In the app directory, ensure you have the following folders:

- `Interfaces/`: Contains interface files, defining the methods for repositories.
- `Repositories/`: Contains the repository implementations for data access.
- `Services/`: Contains service classes for business logic.

**Example Directory Structure:**
```text
app/
├── Http/
├── Interfaces/
│   └── PostRepositoryInterface.php
├── Models/
│   └── Post.php
├── Repositories/
│   └── PostRepository.php
├── Services/
│   └── PostService.php
```

## Step 3: Create the Repository Interface
Define an interface for `PostRepository` in `app/Interfaces/PostRepositoryInterface.php`:

```php
<?php

namespace App\Interfaces;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface PostRepositoryInterface
{
    /**
     * @return Collection<int, Book>
     */
    public function getAllPosts(): Collection;
    
    /**
     * @param int $id
     * @return Book
     */
    public function getPost(int $id): Post;
    
    /**
     * @param array<string, mixed> $data
     * @return Post
     */
    public function createPost(array $data): Post;
    
    /**
     * @param array<string, mixed> $data
     * @param int $id
     * @return Post
     */
    public function updatePost(array $data, int $id): Post;
    
    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deletePost(int $id): JsonResponse;
}

```

## Step 4: Create Custom Exceptions

Before we create the Repositories, we’ll set up custom exceptions. Custom exceptions allow for targeted error messages
and clearer communication when certain operations don’t succeed as expected. For the `Post` repository, we’ll create
exceptions like `PostNotFoundException` and `PostNotDeletedException` to handle specific error cases.

**1. Creating Custom Exceptions**: Use the Artisan command to create these necessary exceptions:

```bash
php artisan make:exception PostNotFoundException
php artisan make:exception PostNotDeletedException

```

These commands generate exception classes in the `app/Exceptions` directory. Each exception can be customized with
specific error messages or HTTP status codes to improve feedback for clients.

**2. Using Custom Exceptions in the Repository**: Later, in the repository (see Step 5), we’ll throw these exceptions
when an operation fails. For example, if a `Post` cannot be found, we’ll throw `PostNotFoundException`. This allows for
structured, readable error handling throughout the application.

```php
<?php

namespace App\Repositories;

use App\Models\Post;
use App\Exceptions\PostNotFoundException;

class PostRepository
{
    public function findById($id)
    {
        $post = Post::find($id);

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$id} not found.");
        }

        return $post;
    }
}

```

**3. Handling Exceptions in the Controller**: In the controllers, you can catch these specific exceptions and return
meaningful error responses to the API clients. This allows clients to receive clear feedback if, for example, a `Post`
doesn’t exist.

### Why Custom Exceptions?

Custom exceptions make the code more modular and readable. Instead of relying on generic errors, we use specific
exceptions that clarify exactly what went wrong. This improves error handling and allows the API to return precise HTTP
status codes and error messages.

With custom exceptions in place, we’re now set to proceed to the next step and create the repository with robust error
handling for data operations.

## Step 5: Create the Repository
Create the repository class that implements this interface in `app/Repositories/PostRepository.php`:

```php
<?php

namespace App\Repositories;

use App\Exceptions\PostNotDeletedException;
use App\Exceptions\PostNotFoundException;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @return Collection<int, ,Post>
     */
    public function getAllPosts(): Collection
    {
        $posts = Post::all();
        return Collection::make($posts);
    }

    /**
     * @param int $id
     * @return Post
     * @throws PostNotFoundException
     */
    public function getPostById(int $id): Post
    {
        $post = Post::find($id);

        if (!$post) {
            throw new PostNotFoundException();
        }

        return $post;
    }

    /**
     * @param array<string, string> $data
     * @return Post
     */
    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * @param array<string, string> $data
     * @param int $id
     * @return Post
     */
    public function updatePost(array $data, int $id): Post
    {
        $post = $this->getPostById($id);
        $post->update($data);
        return $post;
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws PostNotDeletedException
     */
    public function deletePost(int $id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post || !$post->delete()) {
            throw new PostNotDeletedException();
        }

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

```

## Step 6: Bind the Repository to the Interface
In `AppServiceProvider` (`app/Providers/AppServiceProvider.php`), bind the interface to the repository implementation in the register method:

```php
use App\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;

public function register()
{
    $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PostService::class, function ($app) {
            return new PostService($app->make(PostRepositoryInterface::class));
        });
}

```

## Step 7: Create the Service
Create a service class in `app/Services/PostService.php` to contain business logic, if any, and inject the `PostRepositoryInterface`. This keeps business logic separated from data access.

```php
<?php

namespace App\Services;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\PostNotDeletedException;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {
    }

    /**
     * @return Collection<int, Post>
     */
    public function getAllPosts(): Collection
    {
        return $this->postRepository->getAllPosts();
    }

    /**
     * @param int $id
     * @return Post
     * @throws PostNotFoundException
     */
    public function getPostById(int $id): Post
    {
        $post = $this->postRepository->getPostById($id);
        
        if (!$post) {
            throw new PostNotFoundException();
        }

        return $post;
    }

    /**
     * @param array<string, mixed> $data
     * @return Post
     */
    public function createPost(array $data): Post
    {
        return $this->postRepository->createPost($data);
    }

    /** 
     * @param int $id
     * @param array<string, mixed> $data
     * @return Post
     * @throws PostNotFoundException
     */
    public function updatePost(int $id, array $data): Post
    {
        $post = $this->postRepository->getPostById($id);

        if (!$post) {
            throw new PostNotFoundException();
        }

        return $this->postRepository->updatePost($data, $id);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws PostNotFoundException
     * @throws PostNotDeletedException
     */
    public function deletePost(int $id): JsonResponse
    {
        $post = $this->postRepository->getPostById($id);

        if (!$post) {
            throw new PostNotFoundException();
        }

        $deleted = $this->postRepository->deletePost($id);

        if (!$deleted) {
            throw new PostNotDeletedException();
        }

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}

```

## Step 8: Update the Controller
In `PostController`, inject the `PostService` and use it to interact with the data layer:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return PostResourceCollection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return PostResource::collection($posts);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return PostResource
     */
    public function show(int $id)
    {
        $post = $this->postService->show($id);
        return new PostResource($post);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostRequest $request
     * @return PostResource
     */
    public function store(StorePostRequest $request): PostResource
    {
        $post = $this->postService->create($request->validated());
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param int $id
     * @return PostResource
     */
    public function update(UpdatePostRequest $request, int $id)
    {
        $post = $this->postService->update($id, $request->validated());
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool|JsonResponse
     */
    public function destroy(int $id): bool|JsonResponse
    {
        return $this->postService->delete($id);
    }
}

```

## Step 9: Create API Resources

To control the JSON structure of your API responses for the `Post` model, create `PostResource` and
`PostResourceCollection` classes. This allows for clear, consistent responses and gives you fine-grained control over
the data returned.

**1. Generate PostResource:**

```bash
php artisan make:resource PostResource
```

This command creates a `PostResource` class in `app/Http/Resources/PostResource.php`. Update the class to format
individual `Post` objects:

```php
<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

```

Here, only the id, title, content, created_at, and updated_at fields are returned. You can modify this to include
additional fields as needed.

**2. Generate PostResourceCollection:** Laravel automatically wraps collections in a `ResourceCollection` when using the
`PostResource::collection()` method. However, if you want custom behavior, you can create a specific collection
resource:

```bash
php artisan make:resource PostResourceCollection --collection
```

Customize `PostResourceCollection` in `app/Http/Resources/PostResourceCollection.php` to structure the collection
response as needed:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(
                function ($item) use ($request) {
                    return $item instanceof PostResource ? $item->toArray($request) : [];
                }
            )->all(),
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}
```

In this example, we add a `meta` field to provide additional information, such as the total number of items in the
collection.

**3. Use Resources in PostController:** Update `PostController` to return `PostResource` for single Post objects and
`PostResourceCollection` for lists of Post objects. Here’s how:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return PostResourceCollection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return new PostResourceCollection($posts);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return PostResource
     */
    public function show(int $id)
    {
        $post = $this->postService->show($id);
        return new PostResource($post);
    }
}

```

Now, whenever `index` or `show` methods are called, the API response will be formatted according to `PostResource` and
`PostResourceCollection`. This keeps your responses consistent and structured across the API.

## Step 10: The "Ugly" Part - Testing for High Coverage

Now we come to what many consider the "ugly" part: Unit and Feature Tests. But if we approach testing systematically,
keeping our test coverage high, we'll not only end up with more stable code but will also come to appreciate (and maybe
even love!) this crucial step in the development process. A strong test suite can save hours of debugging time and
protect against unexpected changes or bugs as the project grows.

In this boilerplate, we’ll focus on setting up Unit Tests for the Service layer, which handles business logic, and
Feature Tests to validate that API endpoints work as expected.

### 10.1. Setting Up the Testing Environment

Make sure your .env.testing file is correctly configured for testing purposes. Typically, this involves setting up a
separate database and cache:

```text
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array

```

Then, run the migrations for the testing environment:

```bash
php artisan migrate --env=testing

```

### 10.2. Writing Unit Tests for Services

Services contain business logic, so testing them directly will give you confidence that core functionalities behave as
intended. Here’s an example of a Unit Test for the `PostService`:

**1. Create the Test Class:**

```bash
php artisan make:test PostServiceTest --unit
```

**2. Write Tests for PostService in `tests/Unit/PostServiceTest.php`:**

```php
<?php

namespace Tests\Unit;

use App\Services\PostService;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $postService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postService = app(PostService::class);
    }

    public function test_it_can_create_a_post()
    {
        $data = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ];

        $post = $this->postService->createPost($data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('Test Post', $post->title);
    }

    // Add more tests for update, delete, and retrieval methods.
}

```

This test checks that the `createPost` method in `PostService` correctly creates a `Post` instance. Add further tests
for `update`, `delete`, and other methods as necessary.

### 10.3. Writing Feature Tests for API Endpoints

Feature Tests allow you to test the application’s HTTP endpoints to ensure they respond as expected. In this
boilerplate, these tests verify that our API controllers and routes return the correct status codes and data formats.

**1. Create a Feature Test for the Post API:**

```bash
php artisan make:test PostApiTest
```

**2. Write Feature Tests in `tests/Feature/PostApiTest.php`:**

```php
<?php

namespace Tests\Feature;

use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_fetch_all_posts()
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_it_can_fetch_a_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $post->id);
    }

    public function test_it_can_create_a_post()
    {
        $data = [
            'title' => 'New Post',
            'content' => 'This is a new post.',
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'New Post');
    }

    // Add further tests for update and delete endpoints.
}

```

These tests validate that the API endpoints respond with the correct data structure and status codes, covering essential
CRUD operations.

### 10.4. Running the Tests

To run the tests, use the following command:

```bash
php artisan test

```

This will execute all Unit and Feature Tests, providing an overview of the test coverage and flagging any issues in the
application logic.

To additionally check for static analysis issues and ensure code quality, you can use PHPStan. PHPStan detects potential
bugs, type issues, and other code quality problems. To run PHPStan, use the following command:

```bash
php vendor/bin/phpstan analyze

```

This command analyzes your code and provides a list of any detected issues, such as type mismatches or potential errors
that could affect stability.

> **Tip:**
> Regularly running PHPStan along with your tests helps maintain high code quality and quickly catches any issues that
> could impact reliability as the project grows.

**Why Embrace Testing?**
Yes, setting up tests might seem tedious at first, but they serve as a safety net for your codebase. With comprehensive
Unit and Feature Tests, you can confidently refactor or extend your application, knowing that existing functionality is
protected. As your project scales, you’ll come to appreciate the stability and reliability that good test coverage
provides.

## Summary
This setup allows you to follow the Repository Pattern in a clean, organized way:

- **Controller**: Manages incoming requests and directs them to the service layer.
- **Service**: Contains business logic and manages data flow by calling repository methods.
- **Repository**: Handles direct data access and manipulations for the `Post` model.

Additionally, Resources are used to format and structure the API responses consistently:

- **Resource**: Defines the structure for individual Post responses, controlling which fields are exposed and in what
  format.
- **Resource Collection**: Provides a standard structure for lists of Post objects, including options for additional
  metadata (like pagination or count) to ensure consistency across collection responses.

Custom Exceptions provide clear, consistent error handling throughout the application:

- **Custom Exceptions**: Specific exceptions like `PostNotFoundException` and `PostNotDeletedException` help manage
  expected error cases, making the codebase more readable and offering informative responses to API clients. Exceptions
  are used in the Service layer, which ensures that error-handling logic is separated from data-access logic.

This approach promotes loose coupling, improves testability, and makes the codebase easier to maintain and extend as
your application grows.

### Testing

To ensure code stability, this project includes Unit and Feature Tests:

- **Unit Tests**: Focus on testing the Service layer’s business logic in isolation. This helps guarantee that core
  functionalities work as intended without dependency on external factors.
- **Feature Tests**: Validate the behavior of API endpoints, confirming that they return the expected status codes and
  data formats. Feature tests provide confidence that the application’s user-facing components work as expected.

Starting with tests early in development is crucial. By building a test suite from the beginning, you create a safety
net that allows for faster debugging, easier refactoring, and confident feature expansion. Consistent testing not only
improves test coverage but also helps maintain code quality and reliability as the application grows.

This approach promotes loose coupling, improves testability, and makes the codebase easier to maintain, supporting
stable and scalable development.
