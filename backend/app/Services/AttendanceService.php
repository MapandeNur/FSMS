<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function checkIn($student): Attendance
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $this->assertNotWeekendOrHoliday($now);

        // TODO: mara 'placements' table itakapokuwepo, angalia hapa
        // kama $student ana active placement kabla ya kuendelea.
        // if (! $student->hasActivePlacement()) {
        //     throw ValidationException::withMessages(['placement' => 'No active placement.']);
        // }

        $existing = Attendance::where('student_id', $student->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in_at) {
            throw ValidationException::withMessages(['check_in' => 'Already checked in today.']);
        }

        $lateAfter = Carbon::parse($today . ' ' . config('fsms.late_after'));
        $status = $now->greaterThan($lateAfter) ? 'late' : 'present';

        return Attendance::updateOrCreate(
            ['student_id' => $student->id, 'attendance_date' => $today],
            [
                'placement_id' => $student->placement_id ?? 0, // muda: hadi placements ikamilike
                'check_in_at' => $now,
                'status' => $status,
                'source' => 'self',
            ]
        );
    }

    public function checkOut($student): Attendance
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $attendance = Attendance::where('student_id', $student->id)
            ->where('attendance_date', $today)
            ->first();

        if (! $attendance || ! $attendance->check_in_at) {
            throw ValidationException::withMessages(['check_out' => 'Must check in before checking out.']);
        }

        if ($attendance->check_out_at) {
            throw ValidationException::withMessages(['check_out' => 'Already checked out today.']);
        }

        $attendance->update(['check_out_at' => $now]);

        return $attendance;
    }

    protected function assertNotWeekendOrHoliday(Carbon $now): void
    {
        $workDays = config('fsms.work_days');

        if (! in_array($now->format('l'), $workDays)) {
            throw ValidationException::withMessages(['attendance_date' => 'No check-in on weekends.']);
        }

        if (Holiday::where('holiday_date', $now->toDateString())->exists()) {
            throw ValidationException::withMessages(['attendance_date' => 'No check-in on holidays.']);
        }
    }
}