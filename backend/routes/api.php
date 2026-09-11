<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
Route::post('/auth/register',[AuthController::class,'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

    });

});