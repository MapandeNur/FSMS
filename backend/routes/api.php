<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\SupervisorLogbookController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SupervisorLeaveController;

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
        Route::middleware('role:supervisor')->group(function () {
            Route::get('/supervisor/logbook', [SupervisorLogbookController::class, 'index']);
            Route::post('/supervisor/logbook/{logbook}/approve', [SupervisorLogbookController::class, 'approve']);
            Route::post('/supervisor/logbook/{logbook}/return', [SupervisorLogbookController::class, 'return']);

            Route::get('/supervisor/leave-requests', [SupervisorLeaveController::class, 'index']);
            Route::post('/supervisor/leave-requests/{leaveRequest}/approve', [SupervisorLeaveController::class, 'approve']);
            Route::post('/supervisor/leave-requests/{leaveRequest}/reject', [SupervisorLeaveController::class, 'reject']);
        });


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
            Route::get('/logbook', [LogbookController::class, 'index']);
            Route::post('/logbook', [LogbookController::class, 'store']);
            Route::put('/logbook/{logbook}', [LogbookController::class, 'update']);
            Route::post('/logbook/{logbook}/submit', [LogbookController::class, 'submit']);

            Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
            Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
            Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy']);
        });
    });    

});