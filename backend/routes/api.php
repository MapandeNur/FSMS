<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    // Public login route
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Logout
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Get logged-in user
        Route::get('/auth/me', [AuthController::class, 'me']);

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')->group(function () {

            // User CRUD
            Route::apiResource('users', UserController::class);

        });

    });

});