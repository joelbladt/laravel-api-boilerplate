<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property LengthAwarePaginator<self> $resource
 */
class BaseResourceCollection extends ResourceCollection
{
    /**
     * @return array<null>
     */
    public function paginationInformation(Request $request): array
    {
        return [
        ];
    }
}
