<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorLeaveController extends Controller
{
    protected LeaveService $service;

    public function __construct(LeaveService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        // TODO: mara supervisor-student link itakapokuwepo, chuja kwa wanafunzi wa huyu supervisor pekee.
        return response()->json(['data' => LeaveRequest::orderByDesc('start_date')->get()]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $leave = $this->service->approve($leaveRequest, $request->user());

        return response()->json(['data' => $leave]);
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $leave = $this->service->reject($leaveRequest, $request->user(), $data['comment']);

        return response()->json(['data' => $leave]);
    }
}