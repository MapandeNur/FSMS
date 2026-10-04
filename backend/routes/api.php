<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StudentDocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Authentication Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');


    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/auth/me', [AuthController::class, 'me']);
           /*
    |--------------------------------------------------------------------------
    | Department Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('departments', DepartmentController::class);

        /*
        |--------------------------------------------------------------------------
        | Student Document Routes
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/students/{student}/documents',
            [StudentDocumentController::class, 'store']
        );

        Route::get(
            '/students/{student}/documents',
            [StudentDocumentController::class, 'index']
        );

        Route::get(
            '/documents/{studentDocument}/download',
            [StudentDocumentController::class, 'download']
        );

        Route::delete(
            '/documents/{studentDocument}',
            [StudentDocumentController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')->group(function () {

            Route::apiResource('users', UserController::class);

            Route::apiResource('students', StudentController::class);

        });

    });

});