<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $service;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    public function checkIn(Request $request): JsonResponse
    {
        $student = Student::where('email', $request->user()->email)->firstOrFail();

        $attendance = $this->service->checkIn($student);

        return response()->json(['data' => $attendance]);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $student = Student::where('email', $request->user()->email)->firstOrFail();

        $attendance = $this->service->checkOut($student);

        return response()->json(['data' => $attendance]);
    }

    public function today(Request $request): JsonResponse
    {
        $student = Student::where('email', $request->user()->email)->firstOrFail();

        $attendance = \App\Models\Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        return response()->json(['data' => $attendance]);
    }

    public function mine(Request $request): JsonResponse
    {
        $student = Student::where('email', $request->user()->email)->firstOrFail();

        $query = \App\Models\Attendance::where('student_id', $student->id);

        if ($request->filled('from')) {
            $query->whereDate('attendance_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('attendance_date', '<=', $request->input('to'));
        }

        return response()->json(['data' => $query->orderByDesc('attendance_date')->get()]);
    }
}