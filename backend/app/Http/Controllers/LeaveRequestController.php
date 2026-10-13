<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Student;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    protected LeaveService $service;

    public function __construct(LeaveService $service)
    {
        $this->service = $service;
    }

    protected function student(Request $request): Student
    {
        return Student::where('email', $request->user()->email)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $requests = LeaveRequest::where('student_id', $student->id)
            ->orderByDesc('start_date')
            ->get();

        return response()->json(['data' => $requests]);
    }

    public function store(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'leave_type' => ['required', 'string'],
            'reason' => ['required', 'string'],
        ]);

        $leave = $this->service->create($student, $data);

        return response()->json(['data' => $leave], 201);
    }

    public function destroy(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $student = $this->student($request);

        if ($leaveRequest->student_id !== $student->id) {
            abort(404);
        }

        $this->service->cancel($leaveRequest);

        return response()->json(['message' => 'Leave request cancelled.']);
    }
}