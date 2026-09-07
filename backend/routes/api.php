<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Standalone public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Version 1 API Routes
Route::prefix('v1')->group(function () {

    // Public auth route with rate limiting
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Protected routes requiring Sanctum token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'userProfile']);
    });

});