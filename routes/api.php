<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\PublisherController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'books' => BookController::class,
    'publisher' => PublisherController::class,
]);

Route::get('/publisher/{id}/books', [PublisherController::class, 'showBooks']);

Route::get('/health', fn() => response()->json([
    'status' => 'ok',
]));
