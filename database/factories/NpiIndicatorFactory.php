<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NpiGoal;
use App\Models\NpiIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

class NpiIndicatorFactory extends Factory
{
    protected $model = NpiIndicator::class;

    public function definition(): array
    {
        return [
            'npi_goal_id' => NpiGoal::factory(),
            'indicator_code' => fake()->unique()->numerify('#.#'),
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
        ];
    }
}
