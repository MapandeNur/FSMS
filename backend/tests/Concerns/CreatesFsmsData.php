<?php

namespace Tests\Concerns;

use App\Models\Department;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

trait CreatesFsmsData
{
    private int $dataSequence = 0;

    protected function makeUser(string $roleName, array $overrides = []): User
    {
        $this->dataSequence++;

        $user = User::create(array_merge([
            'name' => ucfirst($roleName) . " {$this->dataSequence}",
            'email' => "{$roleName}{$this->dataSequence}@fsms-test.com",
            'phone' => '07' . str_pad((string) $this->dataSequence, 8, '0', STR_PAD_LEFT),
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

    protected function makeStudent(array $overrides = []): Student
    {
        $this->dataSequence++;

        return Student::create(array_merge([
            'registration_number' => "TEST-STU-{$this->dataSequence}",
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'Student',
            'gender' => 'male',
            'date_of_birth' => '2003-01-15',
            'email' => "student{$this->dataSequence}@fsms-test.com",
            'phone' => '07' . str_pad((string) $this->dataSequence, 8, '0', STR_PAD_LEFT),
            'institution_name' => 'Institute of Finance Management',
            'programme_of_study' => 'Bachelor of Information Technology',
            'year_of_study' => 2,
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
        ], $overrides));
    }

    /**
     * A student user and the student record linked to it by email.
     *
     * @return array{0: User, 1: Student}
     */
    protected function makeStudentUser(): array
    {
        $user = $this->makeUser('student');
        $student = $this->makeStudent(['email' => $user->email]);

        return [$user, $student];
    }

    protected function makeSupervisorUser(): User
    {
        return $this->makeUser('supervisor');
    }

    protected function makeDepartment(array $overrides = []): Department
    {
        $this->dataSequence++;

        return Department::create(array_merge([
            'name' => "Department {$this->dataSequence}",
            'code' => "DEP{$this->dataSequence}",
            'description' => 'Test department',
            'is_active' => true,
        ], $overrides));
    }
}