<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Community Services Block Grant',
                'Emergency Services',
                'Weatherization',
                'Head Start',
                'Housing Assistance',
                'Utility Assistance',
                'Food Pantry',
                'Employment Training',
                'Financial Literacy',
                'Health Services',
            ]),
            'code' => fake()->unique()->bothify('??##'),
            'description' => fake()->sentence(),
            'funding_source' => fake()->randomElement(['CSBG', 'state', 'local', 'private', 'federal']),
            'fiscal_year_start' => now()->startOfYear()->subMonths(3), // Oct 1
            'fiscal_year_end' => now()->startOfYear()->addMonths(9)->subDay(), // Sep 30
            'requires_income_eligibility' => true,
            'fpl_threshold_percent' => fake()->randomElement([125, 150, 200, 250]),
            'is_active' => true,
        ];
    }

    public function noIncomeRequirement(): static
    {
        return $this->state(fn () => [
            'requires_income_eligibility' => false,
            'fpl_threshold_percent' => 0,
        ]);
    }
}
