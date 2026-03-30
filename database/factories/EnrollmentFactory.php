<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $enrolledAt = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'client_id' => Client::factory(),
            'program_id' => Program::factory(),
            'caseworker_id' => User::factory()->caseworker(),
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => $enrolledAt,
            'completed_at' => null,
            'household_income_at_enrollment' => fake()->numberBetween(8000, 45000),
            'household_size_at_enrollment' => fake()->numberBetween(1, 6),
            'fpl_percent_at_enrollment' => fake()->numberBetween(50, 250),
            'income_eligible' => true,
            'eligibility_notes' => null,
            'denial_reason' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Completed,
            'completed_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function denied(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Denied,
            'income_eligible' => false,
            'denial_reason' => 'Over income threshold',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Pending,
        ]);
    }
}
