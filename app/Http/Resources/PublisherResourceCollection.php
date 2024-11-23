<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="PublisherResourceCollection",
 *     type="object",
 *     title="Publisher Resource Collection",
 *     description="A collection of publisher wrapped in a data key",
 *
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         minItems=3,
 *
 *         @OA\Items(ref="#/components/schemas/PublisherResource")
 *     ),
 *
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         ref="#/components/schemas/MetadataResource"
 *     )
 * )
 */
class PublisherResourceCollection extends BaseResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(
                function ($item) use ($request) {
                    return $item instanceof PublisherResource ? $item->toArray($request) : [];
                }
            ),
            'meta' => [
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'total' => $this->resource->total(),
            ],
        ];
    }
}
