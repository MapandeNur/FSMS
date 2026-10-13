<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
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
        $user->roles()->attach($role->id, ['start_date' => now()->toDateString()]);

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
            'end_date' => now()->addMonths(5)->toDateString(),
            'status' => 'active',
        ]);

        return [$user, $student];
    }

    protected function makeSupervisorUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'supervisor'], ['description' => 'Field supervisor']);

        $user = User::create([
            'name' => 'Test Supervisor',
            'email' => 'supervisor@test.com',
            'phone' => '0722222222',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->roles()->attach($role->id, ['start_date' => now()->toDateString()]);

        return $user;
    }

    public function test_student_can_create_leave_request(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_type' => 'sick',
            'reason' => 'Not feeling well',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leave_requests', [
            'student_id' => $student->id,
            'status' => 'pending',
        ]);
    }

    public function test_overlapping_leave_request_is_rejected(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'leave_type' => 'sick',
            'reason' => 'First request',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date' => now()->addDays(8)->toDateString(),
            'leave_type' => 'personal',
            'reason' => 'Overlapping request',
        ]);

        $response->assertStatus(422);
    }

    public function test_student_can_cancel_pending_request(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_type' => 'sick',
            'reason' => 'Not feeling well',
        ]);
        $leaveId = $create->json('data.id');

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/leave-requests/{$leaveId}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveId, 'status' => 'cancelled']);
    }

    public function test_approval_creates_excused_attendance_rows(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $supervisor = $this->makeSupervisorUser();

        $start = now()->addDays(2)->toDateString();
        $end = now()->addDays(4)->toDateString();

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => $start,
            'end_date' => $end,
            'leave_type' => 'sick',
            'reason' => 'Not feeling well',
        ]);
        $leaveId = $create->json('data.id');

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson("/api/v1/supervisor/leave-requests/{$leaveId}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveId, 'status' => 'approved']);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'attendance_date' => $start,
            'status' => 'excused',
            'source' => 'system',
        ]);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'attendance_date' => $end,
            'status' => 'excused',
            'source' => 'system',
        ]);
    }

    public function test_rejection_requires_comment(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $supervisor = $this->makeSupervisorUser();

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/leave-requests', [
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'leave_type' => 'sick',
            'reason' => 'Not feeling well',
        ]);
        $leaveId = $create->json('data.id');

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson("/api/v1/supervisor/leave-requests/{$leaveId}/reject", ['comment' => '']);

        $response->assertStatus(422);
    }
}