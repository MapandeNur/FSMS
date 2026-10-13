<?php

namespace App\Services;

use App\Enums\LogbookStatus;
use App\Models\Attendance;
use App\Models\LogbookEntry;
use Illuminate\Validation\ValidationException;

class LogbookService
{
    public function create($student, array $data): LogbookEntry
    {
        $this->assertEntryDateValid($student, $data['entry_date']);
        $this->assertHoursValid($data['hours_worked']);

        return LogbookEntry::create([
            'student_id' => $student->id,
            'placement_id' => $student->placement_id ?? 0, // muda: hadi placements ikamilike
            'entry_date' => $data['entry_date'],
            'activities' => $data['activities'],
            'skills_learned' => $data['skills_learned'] ?? null,
            'challenges' => $data['challenges'] ?? null,
            'hours_worked' => $data['hours_worked'],
            'status' => LogbookStatus::Draft->value,
        ]);
    }

    public function update(LogbookEntry $entry, array $data): LogbookEntry
    {
        $this->assertEditable($entry);

        if (isset($data['entry_date'])) {
            $this->assertEntryDateValid($entry->student, $data['entry_date']);
        }

        if (isset($data['hours_worked'])) {
            $this->assertHoursValid($data['hours_worked']);
        }

        $entry->update(array_filter([
            'entry_date' => $data['entry_date'] ?? null,
            'activities' => $data['activities'] ?? null,
            'skills_learned' => $data['skills_learned'] ?? null,
            'challenges' => $data['challenges'] ?? null,
            'hours_worked' => $data['hours_worked'] ?? null,
        ], fn ($v) => $v !== null));

        return $entry;
    }

    public function submit(LogbookEntry $entry): LogbookEntry
    {
        if (! in_array($entry->status, [LogbookStatus::Draft->value, LogbookStatus::Returned->value])) {
            throw ValidationException::withMessages(['status' => 'Entry cannot be submitted from its current status.']);
        }

        $entry->update([
            'status' => LogbookStatus::Submitted->value,
            'submitted_at' => now(),
        ]);

        return $entry;
    }

    public function approve(LogbookEntry $entry, $reviewer): LogbookEntry
    {
        if ($entry->status !== LogbookStatus::Submitted->value) {
            throw ValidationException::withMessages(['status' => 'Only submitted entries can be approved.']);
        }

        $entry->update([
            'status' => LogbookStatus::Approved->value,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $entry;
    }

    public function return(LogbookEntry $entry, $reviewer, string $comment): LogbookEntry
    {
        if ($entry->status !== LogbookStatus::Submitted->value) {
            throw ValidationException::withMessages(['status' => 'Only submitted entries can be returned.']);
        }

        if (trim($comment) === '') {
            throw ValidationException::withMessages(['supervisor_comment' => 'A comment is required to return an entry.']);
        }

        $entry->update([
            'status' => LogbookStatus::Returned->value,
            'supervisor_comment' => $comment,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $entry;
    }

    protected function assertEditable(LogbookEntry $entry): void
    {
        if (! in_array($entry->status, [LogbookStatus::Draft->value, LogbookStatus::Returned->value])) {
            throw ValidationException::withMessages(['status' => 'This entry is locked and cannot be edited.']);
        }
    }

    protected function assertEntryDateValid($student, string $entryDate): void
    {
        if (now()->toDateString() < $entryDate) {
            throw ValidationException::withMessages(['entry_date' => 'Entry date cannot be in the future.']);
        }

        $checkedIn = Attendance::where('student_id', $student->id)
            ->where('attendance_date', $entryDate)
            ->whereNotNull('check_in_at')
            ->exists();

        if (! $checkedIn) {
            throw ValidationException::withMessages(['entry_date' => 'You were not checked in on this date.']);
        }
    }

    protected function assertHoursValid($hours): void
    {
        if ($hours < 0.5 || $hours > 12) {
            throw ValidationException::withMessages(['hours_worked' => 'Hours worked must be between 0.5 and 12.']);
        }
    }
}