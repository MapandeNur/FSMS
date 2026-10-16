<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Holiday;
use App\Models\Role;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\HolidaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_and_holiday_seeders_can_run_more_than_once(): void
    {
        $this->seed(DepartmentSeeder::class);
        $this->seed(HolidaySeeder::class);

        $departments = Department::count();
        $holidays = Holiday::count();

        $this->seed(DepartmentSeeder::class);
        $this->seed(HolidaySeeder::class);

        $this->assertGreaterThanOrEqual(5, $departments);
        $this->assertGreaterThan(0, $holidays);
        $this->assertSame($departments, Department::count());
        $this->assertSame($holidays, Holiday::count());
    }

    public function test_seeded_departments_are_active_with_uppercase_codes(): void
    {
        $this->seed(DepartmentSeeder::class);

        foreach (Department::all() as $department) {
            $this->assertTrue($department->is_active);
            $this->assertSame(strtoupper($department->code), $department->code);
        }
    }

    public function test_database_seeder_creates_roles_departments_and_holidays(): void
    {
        $this->seed();

        foreach (['admin', 'supervisor', 'hr_officer', 'student'] as $name) {
            $this->assertDatabaseHas('roles', ['name' => $name]);
        }

        $this->assertGreaterThanOrEqual(5, Department::count());
        $this->assertGreaterThan(0, Holiday::count());
    }
}