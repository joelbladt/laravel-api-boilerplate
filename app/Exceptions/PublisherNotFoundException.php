<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="404PublisherNotFound",
 *     type="object",
 *     title="404 - Publisher not Found",
 *     description="Scheme Error - Publisher not Found",
 *
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(
 *             property="message",
 *             type="string",
 *             example="Publisher can not found"
 *         )
 *     )
 * )
 */
class PublisherNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => 'Publisher can not found',
            ],
        ], 404);
    }
}
