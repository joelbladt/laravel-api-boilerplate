<?php

namespace App\Http\Controllers;

/**
 * @OA\OpenApi(
 *
 *     @OA\Info(
 *         version="1.1.0",
 *         title="Laravel API Boilerplate",
 *         description="API-Documentation for this Boilerplate",
 *     )
 * ),
 *
 * @OA\Parameter(
 *     parameter="identifier",
 *     name="id",
 *     in="path",
 *     required=true,
 *
 *     @OA\Schema(type="integer")
 * )
 *
 * @OA\Parameter(
 *     parameter="itemsPerPage",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     description="Items per page for pagination",
 *
 *     @OA\Schema(
 *         type="integer",
 *         default=10
 *     )
 * )
 *
 * @OA\Parameter(
 *     parameter="currentPage",
 *     name="page",
 *     in="query",
 *     required=false,
 *     description="Current page number for the pagination",
 *
 *     @OA\Schema(
 *         type="integer",
 *         default=1
 *     )
 * )
 * @OA\Schema(
 *     schema="MetadataResource",
 *     type="object",
 *     title="Metadata Schema Resource",
 *     description="Scheme for the Metadata",
 *
 *     @OA\Property(
 *         property="per_page",
 *         type="integer",
 *         description="The number of items displayed per page in the paginated response",
 *         example="10"
 *     ),
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         description="The current page number of the paginated data",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="last_page",
 *         type="integer",
 *         description="The total number of pages available",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="The total number of items in the dataset",
 *         example="3"
 *     )
 * )
 */
abstract class Controller
{
    //
}
