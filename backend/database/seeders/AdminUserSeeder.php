<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(
            [AdminUserSeeder :: class]
        );
        User::updateOrCreate(
            ['email' => 'admin@fsms.com'],
            [
                'name' => 'System Admin',
                'phone' => '0712345678',
                'password' => Hash::make('Admin123@'),
                'is_active' => true,
            ]
            
        );
    }
}