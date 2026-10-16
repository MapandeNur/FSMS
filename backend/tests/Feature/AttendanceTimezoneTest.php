<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesFsmsData;
use Tests\TestCase;

class AttendanceTimezoneTest extends TestCase
{
    use CreatesFsmsData;
    use RefreshDatabase;

    public function test_application_timezone_is_dar_es_salaam(): void
    {
        $this->assertSame('Africa/Dar_es_Salaam', config('app.timezone'));
        $this->assertSame('Africa/Dar_es_Salaam', now()->timezoneName);
    }

    public function test_check_in_is_late_when_local_time_is_after_the_threshold(): void
    {
        [$user, $student] = $this->makeStudentUser();

        // 08:45 in Dar es Salaam is 05:45 UTC. Read as UTC it would look early.
        $this->travelTo(Carbon::parse('2026-10-21 08:45:00', 'Africa/Dar_es_Salaam'));

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in')
            ->assertOk();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'attendance_date' => '2026-10-21',
            'status' => 'late',
        ]);
    }

    public function test_attendance_date_uses_the_local_date_after_midnight(): void
    {
        [$user, $student] = $this->makeStudentUser();

        // 01:00 on Wednesday in Dar es Salaam is still Tuesday in UTC.
        $this->travelTo(Carbon::parse('2026-10-21 01:00:00', 'Africa/Dar_es_Salaam'));

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in')
            ->assertOk();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'attendance_date' => '2026-10-21',
            'status' => 'present',
        ]);
    }
}