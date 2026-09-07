<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator with full system access.',
            ],
            [
                'name' => 'supervisor',
                'description' => 'Field supervisor responsible for supervising students.',
            ],
            [
                'name' => 'hr_officer',
                'description' => 'HR officer responsible for managing field students and staff.',
            ],
            [
                'name' => 'student',
                'description' => 'Field student using the system.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}