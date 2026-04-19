<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PublisherController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => response()->json([
    'status' => 'ok',
]));

Route::post('/auth/login', [AuthController::class, 'login']);

Route::apiResource('books', BookController::class)->only(['index', 'show']);
Route::apiResource('publisher', PublisherController::class)->only(['index', 'show']);
Route::get('/publisher/{id}/books', [PublisherController::class, 'showBooks']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('books', BookController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('publisher', PublisherController::class)->only(['store', 'update', 'destroy']);
});
