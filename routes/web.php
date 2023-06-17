<?php

use App\Controllers\PostController;
use App\Facades\Routing\Route;

return [
    Route::get('posts', [PostController::class, 'index']),
    Route::get('posts/{id}', [PostController::class, 'show']),
    Route::post('posts', [PostController::class, 'store']),
    Route::match(['PUT', 'PATCH'], 'posts/{id}', [PostController::class, 'update']),
    Route::delete('posts/{id}', [PostController::class, 'delete']),
];