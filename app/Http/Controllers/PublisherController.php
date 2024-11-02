<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\PublisherResourceCollection;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Http\JsonResponse;

class PublisherController extends Controller
{
    public function __construct(
        protected publisherService $publisherService
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
        $publisher = $this->publisherService->all();
        return PublisherResource::collection($publisher);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePublisherRequest $request
     * @return PublisherResource
     */
    public function store(StorePublisherRequest $request): PublisherResource
    {
        $publisher = $this->publisherService->create($request->validated());
        return new PublisherResource($publisher);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return PublisherResource
     */
    public function show(int $id): PublisherResource
    {
        $user = $this->publisherService->show($id);
        return new PublisherResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePublisherRequest $request
     * @param int $id
     * @return PublisherResource
     */
    public function update(UpdatePublisherRequest $request, int $id): PublisherResource
    {
        $publisher = $this->publisherService->update($id, $request->validated());
        return new PublisherResource($publisher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return bool|JsonResponse
     */
    public function destroy(int $id): bool|JsonResponse
    {
        return $this->publisherService->delete($id);
    }
}
