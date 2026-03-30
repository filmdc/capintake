<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Caseworker,
            'phone' => fake()->phoneNumber(),
            'title' => fake()->randomElement(['Case Manager', 'Intake Specialist', 'Program Coordinator']),
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin, 'title' => 'Administrator']);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => ['role' => UserRole::Supervisor, 'title' => 'Supervisor']);
    }

    public function caseworker(): static
    {
        return $this->state(fn () => ['role' => UserRole::Caseworker]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
