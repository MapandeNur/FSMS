<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogbookTest extends TestCase
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

    protected function checkInToday($student): void
    {
        Attendance::create([
            'student_id' => $student->id,
            'placement_id' => 0,
            'attendance_date' => now()->toDateString(),
            'check_in_at' => now(),
            'status' => 'present',
            'source' => 'self',
        ]);
    }

    public function test_student_can_create_draft_entry_for_checked_in_day(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $this->checkInToday($student);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Worked on reports',
            'hours_worked' => 8,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('logbook_entries', [
            'student_id' => $student->id,
            'status' => 'draft',
        ]);
    }

    public function test_entry_rejected_if_not_checked_in_that_day(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Worked on reports',
            'hours_worked' => 8,
        ]);

        $response->assertStatus(422);
    }

    public function test_entry_rejected_for_invalid_hours(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $this->checkInToday($student);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Worked on reports',
            'hours_worked' => 15,
        ]);

        $response->assertStatus(422);
    }

    public function test_full_logbook_lifecycle_submit_return_edit_resubmit_approve(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $supervisor = $this->makeSupervisorUser();
        $this->checkInToday($student);

        // Create draft
        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Initial activities',
            'hours_worked' => 8,
        ]);
        $entryId = $create->json('data.id');

        // Submit
        $submit = $this->actingAs($user, 'sanctum')->postJson("/api/v1/logbook/{$entryId}/submit");
        $submit->assertStatus(200);
        $this->assertDatabaseHas('logbook_entries', ['id' => $entryId, 'status' => 'submitted']);

        // Supervisor returns with comment
        $return = $this->actingAs($supervisor, 'sanctum')
            ->postJson("/api/v1/supervisor/logbook/{$entryId}/return", ['comment' => 'Please add more detail']);
        $return->assertStatus(200);
        $this->assertDatabaseHas('logbook_entries', ['id' => $entryId, 'status' => 'returned']);

        // Student edits returned entry
        $edit = $this->actingAs($user, 'sanctum')->putJson("/api/v1/logbook/{$entryId}", [
            'activities' => 'More detailed activities now',
        ]);
        $edit->assertStatus(200);

        // Student resubmits
        $resubmit = $this->actingAs($user, 'sanctum')->postJson("/api/v1/logbook/{$entryId}/submit");
        $resubmit->assertStatus(200);
        $this->assertDatabaseHas('logbook_entries', ['id' => $entryId, 'status' => 'submitted']);

        // Supervisor approves
        $approve = $this->actingAs($supervisor, 'sanctum')->postJson("/api/v1/supervisor/logbook/{$entryId}/approve");
        $approve->assertStatus(200);
        $this->assertDatabaseHas('logbook_entries', ['id' => $entryId, 'status' => 'approved']);
    }

    public function test_approved_entry_cannot_be_edited(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $supervisor = $this->makeSupervisorUser();
        $this->checkInToday($student);

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Initial activities',
            'hours_worked' => 8,
        ]);
        $entryId = $create->json('data.id');

        $this->actingAs($user, 'sanctum')->postJson("/api/v1/logbook/{$entryId}/submit");
        $this->actingAs($supervisor, 'sanctum')->postJson("/api/v1/supervisor/logbook/{$entryId}/approve");

        $edit = $this->actingAs($user, 'sanctum')->putJson("/api/v1/logbook/{$entryId}", [
            'activities' => 'Trying to edit after approval',
        ]);

        $edit->assertStatus(422);
    }

    public function test_return_requires_comment(): void
    {
        [$user, $student] = $this->makeStudentUser();
        $supervisor = $this->makeSupervisorUser();
        $this->checkInToday($student);

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logbook', [
            'entry_date' => now()->toDateString(),
            'activities' => 'Initial activities',
            'hours_worked' => 8,
        ]);
        $entryId = $create->json('data.id');
        $this->actingAs($user, 'sanctum')->postJson("/api/v1/logbook/{$entryId}/submit");

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson("/api/v1/supervisor/logbook/{$entryId}/return", ['comment' => '']);

        $response->assertStatus(422);
    }
}