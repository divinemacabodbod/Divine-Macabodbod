<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// CRUD API Routes for Users
Route::apiResource('users', UserController::class);
