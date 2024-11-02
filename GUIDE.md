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
4. [Create the Repository](#step-4-create-the-repository)
5. [Bind the Repository to the Interface](#step-5-bind-the-repository-to-the-interface)
6. [Create the Service](#step-6-create-the-service)
7. [Update the Controller](#step-7-update-the-controller)
8. [Summary](#summary)

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
    public function getAllPosts(): Collection;
    public function getPost(int $id): Post;
    public function createPost(array $data): Post;
    public function updatePost(array $data, int $id): Post;
    public function deletePost(int $id): JsonResponse;
}

```

## Step 4: Create the Repository
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

## Step 5: Bind the Repository to the Interface
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

## Step 6: Create the Service
Create a service class in `app/Services/PostService.php` to contain business logic, if any, and inject the `PostRepositoryInterface`. This keeps business logic separated from data access.

```php
<?php

namespace App\Services;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

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
     */
    public function getPostById(int $id): Post
    {
        return $this->postRepository->getPostById($id);
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
     */
    public function updatePost(int $id, array $data): Post
    {
        return $this->postRepository->updatePost($data, $id);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deletePost(int $id): JsonResponse
    {
        return $this->postRepository->deletePost($id);
    }
}

```

## Step 7: Update the Controller
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

## Summary
This setup allows you to follow the Repository Pattern in a clean, organized way:

- **Controller**: Manages incoming requests and directs them to the service layer.
- **Service**: Contains business logic and manages data flow by calling repository methods.
- **Repository**: Handles direct data access and manipulations for the `Post` model.

This approach promotes loose coupling, improves testability, and makes the codebase easier to maintain as your application grows.
