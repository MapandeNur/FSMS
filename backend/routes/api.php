<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

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

    Route::get('/roles', [RoleController::class, 'index']);
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);

    Route::apiResource('users', UserController::class);

    Route::post('/users/{user}/roles', [UserController::class, 'assignRole']);
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole']);

    Route::apiResource('students', StudentController::class);
});

    
       Route::middleware('role:student')->group(function () {
            Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
            Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
            Route::get('/attendance/today', [AttendanceController::class, 'today']);
            Route::get('/attendance/mine', [AttendanceController::class, 'mine']);
        });
    });    

});