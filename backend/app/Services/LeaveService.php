<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeaveService
{
    protected array $validTypes = ['sick', 'academic', 'personal', 'other'];

    public function create($student, array $data): LeaveRequest
    {
        $this->assertValidType($data['leave_type']);
        $this->assertDatesWithinPlacement($student, $data['start_date'], $data['end_date']);
        $this->assertNoOverlap($student, $data['start_date'], $data['end_date']);

        return LeaveRequest::create([
            'student_id' => $student->id,
            'placement_id' => $student->placement_id ?? 0, // muda: hadi placements ikamilike
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'leave_type' => $data['leave_type'],
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);
    }

    public function cancel(LeaveRequest $request): LeaveRequest
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Only pending requests can be cancelled.']);
        }

        $request->update(['status' => 'cancelled']);

        return $request;
    }

    public function approve(LeaveRequest $request, $reviewer): LeaveRequest
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Only pending requests can be approved.']);
        }

        DB::transaction(function () use ($request, $reviewer) {
            $request->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $period = \Carbon\CarbonPeriod::create($request->start_date, $request->end_date);

            foreach ($period as $date) {
                Attendance::updateOrCreate(
                    ['student_id' => $request->student_id, 'attendance_date' => $date->toDateString()],
                    [
                        'placement_id' => $request->placement_id,
                        'status' => 'excused',
                        'source' => 'system',
                    ]
                );
            }
        });

        return $request;
    }

    public function reject(LeaveRequest $request, $reviewer, string $comment): LeaveRequest
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Only pending requests can be rejected.']);
        }

        if (trim($comment) === '') {
            throw ValidationException::withMessages(['review_comment' => 'A comment is required to reject a request.']);
        }

        $request->update([
            'status' => 'rejected',
            'review_comment' => $comment,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $request;
    }

    protected function assertValidType(string $type): void
    {
        if (! in_array($type, $this->validTypes)) {
            throw ValidationException::withMessages(['leave_type' => 'Invalid leave type.']);
        }
    }

    protected function assertDatesWithinPlacement($student, string $start, string $end): void
    {
        // TODO: mara 'placements' table itakapokuwepo, angalia hapa
        // kama $start/$end ziko ndani ya active placement ya $student.
    }

    protected function assertNoOverlap($student, string $start, string $end): void
    {
        $overlap = LeaveRequest::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages(['dates' => 'These dates overlap with another pending or approved request.']);
        }
    }
}