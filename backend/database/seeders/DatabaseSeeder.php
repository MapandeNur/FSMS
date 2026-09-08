<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
    /**
     * Seed the application's database.
     */
=======
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
