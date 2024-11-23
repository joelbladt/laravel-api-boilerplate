<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="BookResourceCollection",
 *     type="object",
 *     title="Book Resource Collection",
 *     description="A collection of books wrapped in a data key",
 *
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         minItems=3,
 *
 *         @OA\Items(ref="#/components/schemas/BookResource")
 *     ),
 *
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         ref="#/components/schemas/MetadataResource"
 *     )
 * )
 */
class BookResourceCollection extends BaseResourceCollection
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
                    return $item instanceof BookResource ? $item->toArray($request) : [];
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
