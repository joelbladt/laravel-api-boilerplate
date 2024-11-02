<?php

namespace App\Http\Controllers;

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
        $books = $this->bookService->all();
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBookRequest $request
     * @return BookResource
     */
    public function store(StoreBookRequest $request): BookResource
    {
        $book = $this->bookService->create($request->validated());
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return BookResource
     */
    public function show(int $id): BookResource
    {
        $book = $this->bookService->show($id);
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBookRequest $request
     * @param int $id
     * @return BookResource
     */
    public function update(UpdateBookRequest $request, int $id): BookResource
    {
        $book = $this->bookService->update($id, $request->validated());
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool|JsonResponse
     */
    public function destroy(int $id): bool|JsonResponse
    {
        return $this->bookService->delete($id);
    }
}
