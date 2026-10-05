<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function makeStudentUser(): array
    {
        $role = Role::create(['name' => 'student', 'description' => 'Field student']);

        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'phone' => '0700000000',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->roles()->attach($role->id, ['start_date'=>now()->toDateString()]);

        $student = Student::create([
    'registration_number' => 'REG001',
    'first_name' => 'Test',
    'last_name' => 'Student',
    'gender' => 'male',
    'date_of_birth' => '2000-01-01',
    'email' => 'student@test.com',
    'phone' => '0711111111',
    'institution_name' => 'Test Institution',
    'programme_of_study' => 'IT',
    'year_of_study' => 1,
    'start_date' => now()->subMonth()->toDateString(),
    'end_date' => now()->addMonth(5)->toDateString(),
    'status' => 'active',
]);

        return [$user, $student];
    }

    public function test_successful_check_in(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in');

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'present',
        ]);
    }
    public function test_late_check_in_is_marked_late(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $this->travelTo(now()->startOfDay()->setTime(9, 0));

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in');

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'late',
        ]);
    }

    public function test_double_check_in_is_rejected(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance/check-in');
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance/check-in');

        $response->assertStatus(422);
    }

    public function test_check_out_after_check_in_succeeds(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance/check-in');
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance/check-out');

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
        ]);
        $this->assertNotNull(
            \App\Models\Attendance::where('student_id', $student->id)->first()->check_out_at
        );
    }

    public function test_check_out_before_check_in_is_rejected(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-out');

        $response->assertStatus(422);
    }

    public function test_check_in_on_weekend_is_rejected(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $this->travelTo(now()->next(\Carbon\Carbon::SATURDAY)->setTime(9, 0));

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in');

        $response->assertStatus(422);
    }

    public function test_check_in_on_holiday_is_rejected(): void
    {
        [$user, $student] = $this->makeStudentUser();

        \App\Models\Holiday::create([
            'holiday_date' => now()->addDay()->toDateString(),
            'name' => 'Test Holiday',
        ]);

        $this->travelTo(now()->addDay()->setTime(9, 0));

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/check-in');

        $response->assertStatus(422);
    }
}