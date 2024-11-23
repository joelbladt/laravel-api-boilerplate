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
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/books",
     *     summary="Get a collection of books",
     *     tags={"Books"},
     *
     *     @OA\Parameter(ref="#/components/parameters/itemsPerPage"),
     *     @OA\Parameter(ref="#/components/parameters/currentPage"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="A collection of books",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BookResourceCollection")
     *     )
     * )
     */
    public function index(Request $request): BookResourceCollection
    {
        $perPage = $request->integer('per_page', 10);
        $page = $request->integer('page', 1);

        $books = $this->bookService->getAllBooks($perPage, $page);

        return new BookResourceCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post (
     *     path="/api/books",
     *     description="Created a new book",
     *     tags={"Books"},
     *
     *     @OA\RequestBody(
     *         description="Store a new Book",
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreBookRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BookResource")
     *     )
     * )
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->validated());

        return response()->json(new BookResource($book), 201);
    }

    /**
     * Display the specified resource.
     *
     * @throws BookNotFoundException
     *
     * @OA\Get(
     *     path="/api/books/{id}",
     *     description="Display the specified book",
     *     tags={"Books"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BookResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Book not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404BookNotFound")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $book = $this->bookService->findBookById($id);

        return response()->json(new BookResource($book));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws BookNotFoundException|PublisherNotFoundException
     *
     * @OA\Put(
     *     path="/api/books/{id}",
     *     description="Update the specified book by replacing all properties",
     *     tags={"Books"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\RequestBody(
     *         description="Update the specified book by replacing all properties",
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateBookRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BookResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Book not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404BookNotFound")
     *     )
     * )
     */
    public function update(UpdateBookRequest $request, int $id): JsonResponse
    {
        $book = $this->bookService->updateBook($id, $request->validated());

        return response()->json(new BookResource($book), JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     description="Delete the specified book entirely",
     *     tags={"Books"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\Response(response=204, description="Success"),
     *     @OA\Response(
     *         response=404,
     *         description="Book not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404BookNotFound")
     *      )
     * )
     */
    public function destroy(int $id): bool|JsonResponse
    {
        $this->bookService->deleteBook($id);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
