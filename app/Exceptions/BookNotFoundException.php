<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="404BookNotFound",
 *     type="object",
 *     title="404 - Book not Found",
 *     description="Scheme Error - Book not Found",
 *
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(
 *             property="message",
 *             type="string",
 *             example="Book can not found"
 *         )
 *     )
 * )
 */
class BookNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => 'Book can not found',
            ],
        ], 404);
    }
}
