<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@fsms.com'],
            [
                'name' => 'System Admin',
                'phone' => '0712345678',
                'password' => Hash::make('Robby@2026'),
                'is_active' => true,
            ]
        );

        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $admin->roles()->syncWithoutDetaching([
            $adminRole->id => [
                'start_date' => now()->toDateString(),
                'end_date' => null,
            ],
        ]);
    }
}