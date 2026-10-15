<?php

namespace App\Services;

use App\Enums\PlacementStatus;
use App\Models\Department;
use App\Models\Placement;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlacementService
{
    private const RELATIONS = ['student', 'department', 'supervisor', 'assignedBy'];

    public function history(Student $student): Collection
    {
        return Placement::query()
            ->where('student_id', $student->id)
            ->with(self::RELATIONS)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();
    }

    public function place(Student $student, array $data, User $assignedBy): Placement
    {
        return DB::transaction(function () use ($student, $data, $assignedBy) {
            // Lock order is always: student, then placement, then supervisor.
            // The student row is locked first so two requests for the same
            // student are processed one after the other.
            $locked = Student::query()->lockForUpdate()->findOrFail($student->id);

            return $this->createPlacement($locked, $data, $assignedBy);
        });
    }

    public function end(Placement $placement, string $reason): Placement
    {
        return DB::transaction(function () use ($placement, $reason) {
            Student::query()->lockForUpdate()->findOrFail($placement->student_id);

            $locked = Placement::query()->lockForUpdate()->findOrFail($placement->id);

            $this->assertActive($locked);
            $this->close($locked, $reason);

            return $locked->load(self::RELATIONS);
        });
    }

    public function transfer(Placement $current, array $data, User $assignedBy): Placement
    {
        // One transaction: if the new placement is rejected, the old one
        // stays active. Either both changes happen or neither does.
        return DB::transaction(function () use ($current, $data, $assignedBy) {
            $student = Student::query()->lockForUpdate()->findOrFail($current->student_id);
            $locked = Placement::query()->lockForUpdate()->findOrFail($current->id);

            $this->assertActive($locked);
            $this->close($locked, $data['reason']);

            return $this->createPlacement($student, $data, $assignedBy);
        });
    }

    private function createPlacement(Student $student, array $data, User $assignedBy): Placement
    {
        $department = Department::query()->findOrFail($data['department_id']);

        // Locking the supervisor row makes capacity checks for the same
        // supervisor run one after the other.
        $supervisor = User::query()->lockForUpdate()->findOrFail($data['supervisor_id']);

        $this->assertNoActivePlacement($student);
        $this->assertDepartmentActive($department);
        $this->assertSupervisorEligible($supervisor);
        $this->assertDatesWithinAttachment($student, $data['start_date'], $data['end_date']);

        $placement = Placement::create([
            'student_id' => $student->id,
            'department_id' => $department->id,
            'supervisor_id' => $supervisor->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => PlacementStatus::ACTIVE,
            'assigned_by' => $assignedBy->id,
        ]);

        return $placement->load(self::RELATIONS);
    }

    private function close(Placement $placement, string $reason): void
    {
        $placement->update([
            'status' => PlacementStatus::ENDED,
            'ended_reason' => $reason,
        ]);
    }

    private function assertActive(Placement $placement): void
    {
        if ($placement->status !== PlacementStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'placement' => 'Only an active placement can be ended or transferred.',
            ]);
        }
    }

    private function assertNoActivePlacement(Student $student): void
    {
        $hasActive = Placement::query()
            ->where('student_id', $student->id)
            ->where('status', PlacementStatus::ACTIVE->value)
            ->exists();

        if ($hasActive) {
            throw ValidationException::withMessages([
                'student' => 'This student already has an active placement.',
            ]);
        }
    }

    private function assertDepartmentActive(Department $department): void
    {
        if (! $department->is_active) {
            throw ValidationException::withMessages([
                'department_id' => 'Inactive departments cannot receive new placements.',
            ]);
        }
    }

    private function assertSupervisorEligible(User $supervisor): void
    {
        if (! $supervisor->is_active || ! $supervisor->hasRole('supervisor')) {
            throw ValidationException::withMessages([
                'supervisor_id' => 'The selected user is not an active supervisor.',
            ]);
        }

        $limit = (int) config('fsms.max_students_per_supervisor');

        $activeCount = Placement::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('status', PlacementStatus::ACTIVE->value)
            ->count();

        if ($activeCount >= $limit) {
            throw ValidationException::withMessages([
                'supervisor_id' => "This supervisor already has the maximum of {$limit} active students.",
            ]);
        }
    }

    private function assertDatesWithinAttachment(Student $student, string $startDate, string $endDate): void
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        if ($start < Carbon::parse($student->start_date)->toDateString() || $end > Carbon::parse($student->end_date)->toDateString()) {
            throw ValidationException::withMessages([
                'start_date' => 'Placement dates must fall within the student\'s attachment period.',
            ]);
        }
    }
}