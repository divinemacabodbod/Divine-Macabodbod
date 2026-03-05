<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

// Public Authentication Routes (no middleware required)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected Routes (require API token authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // User Management Routes with permission middleware
    Route::apiResource('users', UserController::class);

    // Post Management Routes with Policy Authorization
    // The authorization is handled in the Controller using the Policy
    Route::get('/posts', [PostController::class, 'index']); // Check: list posts
    Route::post('/posts', [PostController::class, 'store']); // Check: create posts
    Route::get('/posts/{post}', [PostController::class, 'show']); // Check: read posts
    Route::put('/posts/{post}', [PostController::class, 'update']); // Check: update posts
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); // Check: delete posts
    Route::post('/posts/{post}/publish', [PostController::class, 'publish']); // Check: publish posts
});

