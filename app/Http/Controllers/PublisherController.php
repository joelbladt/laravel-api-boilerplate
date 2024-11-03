<?php

namespace App\Http\Controllers;

use App\Exceptions\PublisherNotFoundException;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\PublisherResourceCollection;
use App\Services\PublisherService;
use Illuminate\Http\JsonResponse;

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
     * @return PublisherResourceCollection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $publisher = $this->publisherService->getAllPublisher();
        return new PublisherResourceCollection($publisher);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePublisherRequest $request
     * @return JsonResponse
     */
    public function store(StorePublisherRequest $request): JsonResponse
    {
        $publisher = $this->publisherService->createPublisher($request->validated());
        return response()->json(new PublisherResource($publisher), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws PublisherNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $publisher = $this->publisherService->findPublisherById($id);
        return response()->json(new PublisherResource($publisher));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePublisherRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws PublisherNotFoundException
     */
    public function update(UpdatePublisherRequest $request, int $id): JsonResponse
    {
        $publisher = $this->publisherService->updatePublisher($id, $request->validated());
        return response()->json(new PublisherResource($publisher), JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool|JsonResponse
     */
    public function destroy(int $id): bool|JsonResponse
    {
        $this->publisherService->deletePublisher($id);
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
