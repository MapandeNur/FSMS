
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\SupervisorLogbookController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SupervisorLeaveController;

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
        | Department and Placement Routes
        |--------------------------------------------------------------------------
        */

        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('placements', PlacementController::class);


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


        /*
        |--------------------------------------------------------------------------
        | Student Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:student')->group(function () {

            // Attendance
            Route::post(
                '/attendance/check-in',
                [AttendanceController::class, 'checkIn']
            );

            Route::post(
                '/attendance/check-out',
                [AttendanceController::class, 'checkOut']
            );

            Route::get(
                '/attendance/today',
                [AttendanceController::class, 'today']
            );

            Route::get(
                '/attendance/mine',
                [AttendanceController::class, 'mine']
            );


            // Student logbook
            Route::get('/logbook', [LogbookController::class, 'index']);
            Route::post('/logbook', [LogbookController::class, 'store']);

            Route::put(
                '/logbook/{logbook}',
                [LogbookController::class, 'update']
            );

            Route::post(
                '/logbook/{logbook}/submit',
                [LogbookController::class, 'submit']
            );


            // Student leave requests
            Route::get(
                '/leave-requests',
                [LeaveRequestController::class, 'index']
            );

            Route::post(
                '/leave-requests',
                [LeaveRequestController::class, 'store']
            );

            Route::delete(
                '/leave-requests/{leaveRequest}',
                [LeaveRequestController::class, 'destroy']
            );

        });
        Route::get('/placements', [PlacementController::class, 'index']);


        /*
        |--------------------------------------------------------------------------
        | Supervisor Routes
        |--------------------------------------------------------------------------
        |
        | These routes assume your supervisor controllers have the methods
        | approve(), reject(), and return().
        |
        */

        Route::middleware('role:supervisor')->prefix('supervisor')->group(function () {

            // Supervisor leave requests
            Route::post(
                '/leave-requests/{leaveId}/approve',
                [SupervisorLeaveController::class, 'approve']
            );

            Route::post(
                '/leave-requests/{leaveId}/reject',
                [SupervisorLeaveController::class, 'reject']
            );


            // Supervisor logbook
            Route::post(
                '/logbook/{entryId}/return',
                [SupervisorLogbookController::class, 'return']);
        });

    });

});
