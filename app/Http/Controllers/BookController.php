<?php

namespace App\Http\Controllers;

use App\Exceptions\BookNotFoundException;
use App\Exceptions\PublisherNotFoundException;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookResourceCollection;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return BookResourceCollection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $books = $this->bookService->getAllBooks();
        return new BookResourceCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBookRequest $request
     * @return JsonResponse
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->validated());
        return response()->json(new BookResource($book), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws BookNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $book = $this->bookService->findBookById($id);
        return response()->json(new BookResource($book));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBookRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws PublisherNotFoundException
     */
    public function update(UpdateBookRequest $request, int $id): JsonResponse
    {
        $book = $this->bookService->updateBook($id, $request->validated());
        return response()->json(new BookResource($book), JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool|JsonResponse
     */
    public function destroy(int $id): bool|JsonResponse
    {
        $this->bookService->deleteBook($id);
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
