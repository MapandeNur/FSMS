<?php

namespace Tests\Feature;

use App\Enums\PlacementStatus;
use App\Models\Department;
use App\Models\Placement;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PlacementTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private User $hr;
    private User $supervisor;
    private Student $student;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hr = $this->makeUser('hr_officer');
        $this->supervisor = $this->makeUser('supervisor');
        $this->student = $this->makeStudent($this->hr);
        $this->department = $this->makeDepartment();
    }

    // ---------------------------------------------------------------
    // Happy paths
    // ---------------------------------------------------------------

    public function test_hr_officer_can_place_student(): void
    {
        $response = $this->placeStudent($this->payload());

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Student placed successfully.',
            ]);

        $this->assertDatabaseHas('placements', [
            'student_id' => $this->student->id,
            'department_id' => $this->department->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'active',
            'assigned_by' => $this->hr->id,
        ]);
    }

    public function test_admin_can_place_student(): void
    {
        $admin = $this->makeUser('admin');

        $this->placeStudent($this->payload(), $admin)->assertCreated();

        $this->assertDatabaseHas('placements', [
            'student_id' => $this->student->id,
            'assigned_by' => $admin->id,
        ]);
    }

    public function test_placement_history_lists_every_placement_of_the_student(): void
    {
        $first = $this->activePlacement($this->student);
        $first->update([
            'status' => PlacementStatus::ENDED,
            'ended_reason' => 'Finished first rotation',
        ]);
        $this->activePlacement($this->student);

        $this->actingAs($this->hr, 'sanctum')
            ->getJson($this->placementsUrl())
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_hr_officer_can_end_an_active_placement(): void
    {
        $placement = $this->activePlacement($this->student);

        $this->actingAs($this->hr, 'sanctum')
            ->patchJson("/api/v1/placements/{$placement->id}/end", [
                'reason' => 'Completed early',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'ended');

        $this->assertDatabaseHas('placements', [
            'id' => $placement->id,
            'status' => 'ended',
            'ended_reason' => 'Completed early',
        ]);
    }

    public function test_transfer_ends_old_placement_and_creates_a_new_one(): void
    {
        $old = $this->activePlacement($this->student);
        $newDepartment = $this->makeDepartment();
        $newSupervisor = $this->makeUser('supervisor');

        $this->actingAs($this->hr, 'sanctum')
            ->postJson("/api/v1/placements/{$old->id}/transfer", $this->payload([
                'department_id' => $newDepartment->id,
                'supervisor_id' => $newSupervisor->id,
                'reason' => 'Moved to finance',
            ]))
            ->assertCreated()
            ->assertJsonPath('data.department.id', $newDepartment->id);

        $this->assertDatabaseHas('placements', [
            'id' => $old->id,
            'status' => 'ended',
            'ended_reason' => 'Moved to finance',
        ]);

        $this->assertDatabaseHas('placements', [
            'student_id' => $this->student->id,
            'department_id' => $newDepartment->id,
            'supervisor_id' => $newSupervisor->id,
            'status' => 'active',
        ]);

        $this->assertSame(1, $this->activeCountForStudent($this->student));
    }

    public function test_supervisor_below_capacity_can_still_receive_a_student(): void
    {
        config(['fsms.max_students_per_supervisor' => 2]);

        $this->activePlacement($this->makeStudent($this->hr));

        $this->placeStudent($this->payload())->assertCreated();
    }

    public function test_transfer_to_the_same_supervisor_works_when_the_supervisor_is_full(): void
    {
        config(['fsms.max_students_per_supervisor' => 1]);

        $old = $this->activePlacement($this->student);
        $newDepartment = $this->makeDepartment();

        $this->actingAs($this->hr, 'sanctum')
            ->postJson("/api/v1/placements/{$old->id}/transfer", $this->payload([
                'department_id' => $newDepartment->id,
                'reason' => 'Changed department, same supervisor',
            ]))
            ->assertCreated();

        $this->assertSame(1, $this->activeCountForStudent($this->student));
    }

    // ---------------------------------------------------------------
    // Placement rules (each failure)
    // ---------------------------------------------------------------

    public function test_second_active_placement_is_rejected(): void
    {
        $this->activePlacement($this->student);
        $otherSupervisor = $this->makeUser('supervisor');

        $this->placeStudent($this->payload(['supervisor_id' => $otherSupervisor->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['student']);

        $this->assertSame(1, Placement::where('student_id', $this->student->id)->count());
    }

    public function test_supervisor_over_capacity_is_rejected(): void
    {
        config(['fsms.max_students_per_supervisor' => 2]);

        for ($i = 0; $i < 2; $i++) {
            $this->activePlacement($this->makeStudent($this->hr));
        }

        $this->placeStudent($this->payload())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supervisor_id']);

        $this->assertDatabaseMissing('placements', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_user_without_supervisor_role_is_rejected(): void
    {
        $notSupervisor = $this->makeUser('student');

        $this->placeStudent($this->payload(['supervisor_id' => $notSupervisor->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supervisor_id']);

        $this->assertDatabaseCount('placements', 0);
    }

    public function test_inactive_supervisor_is_rejected(): void
    {
        $inactive = $this->makeUser('supervisor', ['is_active' => false]);

        $this->placeStudent($this->payload(['supervisor_id' => $inactive->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supervisor_id']);

        $this->assertDatabaseCount('placements', 0);
    }

    public function test_inactive_department_cannot_receive_a_placement(): void
    {
        $inactive = $this->makeDepartment(['is_active' => false]);

        $this->placeStudent($this->payload(['department_id' => $inactive->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['department_id']);

        $this->assertDatabaseCount('placements', 0);
    }

    public function test_dates_outside_the_attachment_period_are_rejected(): void
    {
        // The student's attachment period is 2026-09-01 to 2026-12-31.
        $this->placeStudent($this->payload(['start_date' => '2026-08-15']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);

        $this->placeStudent($this->payload(['end_date' => '2027-01-15']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);

        $this->assertDatabaseCount('placements', 0);
    }

    public function test_transfer_to_an_inactive_department_changes_nothing(): void
    {
        $old = $this->activePlacement($this->student);
        $inactive = $this->makeDepartment(['is_active' => false]);

        $this->actingAs($this->hr, 'sanctum')
            ->postJson("/api/v1/placements/{$old->id}/transfer", $this->payload([
                'department_id' => $inactive->id,
                'reason' => 'Should not be applied',
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['department_id']);

        // The transaction rolled back: the old placement is still active
        // and no new placement exists.
        $this->assertDatabaseHas('placements', [
            'id' => $old->id,
            'status' => 'active',
        ]);
        $this->assertSame(1, Placement::count());
    }

    public function test_an_ended_placement_cannot_be_ended_or_transferred_again(): void
    {
        $placement = $this->activePlacement($this->student);
        $placement->update([
            'status' => PlacementStatus::ENDED,
            'ended_reason' => 'Already ended',
        ]);

        $this->actingAs($this->hr, 'sanctum')
            ->patchJson("/api/v1/placements/{$placement->id}/end", ['reason' => 'Again'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['placement']);

        $this->actingAs($this->hr, 'sanctum')
            ->postJson("/api/v1/placements/{$placement->id}/transfer", $this->payload([
                'reason' => 'Again',
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['placement']);
    }

    // ---------------------------------------------------------------
    // Validation failures
    // ---------------------------------------------------------------

    public function test_placing_a_student_requires_all_fields(): void
    {
        $this->placeStudent([])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'department_id',
                'supervisor_id',
                'start_date',
                'end_date',
            ]);
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $this->placeStudent($this->payload([
            'start_date' => '2026-11-30',
            'end_date' => '2026-10-01',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_ending_a_placement_requires_a_reason(): void
    {
        $placement = $this->activePlacement($this->student);

        $this->actingAs($this->hr, 'sanctum')
            ->patchJson("/api/v1/placements/{$placement->id}/end", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }

    public function test_transfer_requires_a_reason(): void
    {
        $placement = $this->activePlacement($this->student);

        $this->actingAs($this->hr, 'sanctum')
            ->postJson("/api/v1/placements/{$placement->id}/transfer", $this->payload())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }

    // ---------------------------------------------------------------
    // Authorisation failures
    // ---------------------------------------------------------------

    public function test_students_and_supervisors_cannot_place_students(): void
    {
        foreach (['student', 'supervisor'] as $roleName) {
            $user = $this->makeUser($roleName);

            $this->placeStudent($this->payload(), $user)->assertForbidden();
        }

        $this->assertDatabaseCount('placements', 0);
    }

    public function test_students_and_supervisors_cannot_end_or_transfer_placements(): void
    {
        $placement = $this->activePlacement($this->student);

        foreach (['student', 'supervisor'] as $roleName) {
            $user = $this->makeUser($roleName);

            $this->actingAs($user, 'sanctum')
                ->patchJson("/api/v1/placements/{$placement->id}/end", ['reason' => 'No'])
                ->assertForbidden();

            $this->actingAs($user, 'sanctum')
                ->postJson("/api/v1/placements/{$placement->id}/transfer", $this->payload([
                    'reason' => 'No',
                ]))
                ->assertForbidden();
        }

        $this->assertDatabaseHas('placements', [
            'id' => $placement->id,
            'status' => 'active',
        ]);
    }

    public function test_students_cannot_view_placement_history(): void
    {
        $user = $this->makeUser('student');

        $this->actingAs($user, 'sanctum')
            ->getJson($this->placementsUrl())
            ->assertForbidden();
    }

    public function test_guests_cannot_place_students(): void
    {
        $this->postJson($this->placementsUrl(), $this->payload())
            ->assertUnauthorized();
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function placementsUrl(): string
    {
        return "/api/v1/students/{$this->student->id}/placements";
    }

    private function placeStudent(array $payload, ?User $actor = null): TestResponse
    {
        return $this->actingAs($actor ?? $this->hr, 'sanctum')
            ->postJson($this->placementsUrl(), $payload);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'department_id' => $this->department->id,
            'supervisor_id' => $this->supervisor->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-11-30',
        ], $overrides);
    }

    private function activeCountForStudent(Student $student): int
    {
        return Placement::query()
            ->where('student_id', $student->id)
            ->where('status', PlacementStatus::ACTIVE->value)
            ->count();
    }

    private function activePlacement(Student $student): Placement
    {
        return Placement::create([
            'student_id' => $student->id,
            'department_id' => $this->department->id,
            'supervisor_id' => $this->supervisor->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-11-30',
            'status' => PlacementStatus::ACTIVE,
            'assigned_by' => $this->hr->id,
        ]);
    }

    private function makeUser(string $roleName, array $overrides = []): User
    {
        $this->sequence++;

        $user = User::create(array_merge([
            'name' => ucfirst($roleName) . " {$this->sequence}",
            'email' => "{$roleName}{$this->sequence}@fsms-test.com",
            'phone' => '07' . str_pad((string) $this->sequence, 8, '0', STR_PAD_LEFT),
            'password' => 'Password123',
            'is_active' => true,
        ], $overrides));

        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['description' => ucfirst($roleName)]
        );

        $user->roles()->attach($role->id, [
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);

        return $user;
    }

    private function makeStudent(User $creator, array $overrides = []): Student
    {
        $this->sequence++;

        return Student::create(array_merge([
            'registration_number' => "TEST-STU-{$this->sequence}",
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'Student',
            'gender' => 'male',
            'date_of_birth' => '2003-01-15',
            'email' => "student{$this->sequence}@fsms-test.com",
            'phone' => '07' . str_pad((string) $this->sequence, 8, '0', STR_PAD_LEFT),
            'institution_name' => 'Institute of Finance Management',
            'programme_of_study' => 'Bachelor of Information Technology',
            'year_of_study' => 2,
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
            'created_by' => $creator->id,
        ], $overrides));
    }

    private function makeDepartment(array $overrides = []): Department
    {
        $this->sequence++;

        return Department::create(array_merge([
            'name' => "Department {$this->sequence}",
            'code' => "DEP{$this->sequence}",
            'description' => 'Test department',
            'is_active' => true,
        ], $overrides));
    }
}