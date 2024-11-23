<?php

namespace App\Http\Controllers;

use App\Exceptions\PublisherNotFoundException;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\BookResourceCollection;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\PublisherResourceCollection;
use App\Services\PublisherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function __construct(
        protected PublisherService $publisherService
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/publisher",
     *      summary="Get a collection of publisher",
     *      tags={"Publisher"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="A collection of publisher",
     *
     *          @OA\JsonContent(ref="#/components/schemas/PublisherResourceCollection")
     *      )
     *  )
     *
     * @return PublisherResourceCollection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $page = $request->integer('page', 1);

        $publisher = $this->publisherService->getAllPublisher($perPage, $page);

        return new PublisherResourceCollection($publisher);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post (
     *     path="/api/publisher",
     *     description="Created a new Publisher.",
     *     tags={"Publisher"},
     *
     *     @OA\RequestBody(
     *         description="Store a new publisher",
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StorePublisherRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *     )
     * )
     */
    public function store(StorePublisherRequest $request): JsonResponse
    {
        $publisher = $this->publisherService->createPublisher($request->validated());

        return response()->json(new PublisherResource($publisher), 201);
    }

    /**
     * Display the specified resource.
     *
     * @throws PublisherNotFoundException
     *
     * @OA\Get(
     *     path="/api/publisher/{id}",
     *     description="Display the specified publisher",
     *     tags={"Publisher"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Publisher not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404PublisherNotFound")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $publisher = $this->publisherService->findPublisherById($id);

        return response()->json(new PublisherResource($publisher));
    }

    /**
     * @throws PublisherNotFoundException
     *
     * @OA\Get(
     *     path="/api/publisher/{id}/books",
     *     summary="Get a collection of books from Publisher",
     *     tags={"Publisher"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Parameter(ref="#/components/parameters/itemsPerPage"),
     *     @OA\Parameter(ref="#/components/parameters/currentPage"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="A collection of books from selected Publisher",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BookResourceCollection")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Publisher not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404PublisherNotFound")
     *     )
     * )
     */
    public function showBooks(int $id): BookResourceCollection
    {
        $perPage = request()->integer('per_page', 10);
        $page = request()->integer('page', 1);

        $books = $this->publisherService->findBooksByPublisherId($id, $perPage, $page);

        return new BookResourceCollection($books);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws PublisherNotFoundException
     *
     * @OA\Put(
     *     path="/api/publisher/{id}",
     *     description="Update the specified publisher by replacing all properties.",
     *     tags={"Publisher"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(
     *          response=404,
     *          description="Publisher not Found",
     *
     *          @OA\JsonContent(ref="#/components/schemas/404PublisherNotFound")
     *      )
     * )
     */
    public function update(UpdatePublisherRequest $request, int $id): JsonResponse
    {
        $publisher = $this->publisherService->updatePublisher($id, $request->validated());

        return response()->json(new PublisherResource($publisher), JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws PublisherNotFoundException
     *
     * @OA\Delete(
     *     path="/api/publisher/{id}",
     *     description="Delete the specified publisher entirely.",
     *     tags={"Publisher"},
     *
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *
     *     @OA\Response(response=204, description="Success"),
     *     @OA\Response(
     *         response=404,
     *         description="Publisher not Found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/404PublisherNotFound")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->publisherService->deletePublisher($id);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
