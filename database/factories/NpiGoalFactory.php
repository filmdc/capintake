<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NpiGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

class NpiGoalFactory extends Factory
{
    protected $model = NpiGoal::class;

    public function definition(): array
    {
        return [
            'goal_number' => fake()->unique()->numberBetween(1, 100),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
        ];
    }
}
