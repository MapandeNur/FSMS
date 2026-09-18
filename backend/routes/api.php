<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    // Public registration route
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Public login route
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');


    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);


        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')->group(function () {

            // User management
            Route::apiResource('users', UserController::class);

            // Assign role to user
            Route::post('/users/{user}/roles', [UserController::class, 'assignRole']);

            // Remove role from user
            Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole']);


            // Student management
            Route::apiResource('students', StudentController::class);

        });

    });

});