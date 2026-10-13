<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\StudentDocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\SupervisorLogbookController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SupervisorLeaveController;

Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Departments and placements
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('placements', PlacementController::class);

        // Student documents
        Route::post('/students/{student}/documents', [StudentDocumentController::class, 'store']);
        Route::get('/students/{student}/documents', [StudentDocumentController::class, 'index']);
        Route::get('/documents/{studentDocument}/download', [StudentDocumentController::class, 'download']);
        Route::delete('/documents/{studentDocument}', [StudentDocumentController::class, 'destroy']);

        // Admin
        Route::middleware('role:admin')->group(function () {
            Route::get('/roles', [RoleController::class, 'index']);
            Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);

            Route::apiResource('users', UserController::class);

            Route::post('/users/{user}/roles', [UserController::class, 'assignRole']);
            Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole']);

            Route::apiResource('students', StudentController::class);
        });

        // Supervisor
        Route::middleware('role:supervisor')->group(function () {
            Route::get('/supervisor/logbook', [SupervisorLogbookController::class, 'index']);
            Route::post('/supervisor/logbook/{logbook}/approve', [SupervisorLogbookController::class, 'approve']);
            Route::post('/supervisor/logbook/{logbook}/return', [SupervisorLogbookController::class, 'return']);

            Route::get('/supervisor/leave-requests', [SupervisorLeaveController::class, 'index']);
            Route::post('/supervisor/leave-requests/{leaveRequest}/approve', [SupervisorLeaveController::class, 'approve']);
            Route::post('/supervisor/leave-requests/{leaveRequest}/reject', [SupervisorLeaveController::class, 'reject']);
        });

        // Student
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