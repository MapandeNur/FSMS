<?php

namespace Database\Factories;

<<<<<<< HEAD
=======
use App\Models\User;
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
<<<<<<< HEAD
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
=======
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
     /**
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
<<<<<<< HEAD
=======

>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
