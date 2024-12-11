<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tags', TagController::class);
    Route::apiResource('posts', PostController::class);
    Route::post('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
});

Route::get('/stats', [StatsController::class, 'index']);