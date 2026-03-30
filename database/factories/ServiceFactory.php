<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->randomElement([
                'Case Management', 'Rental Assistance', 'Utility Payment',
                'Food Box Distribution', 'Job Training Referral', 'Resume Workshop',
                'Financial Counseling', 'Transportation Assistance',
                'Clothing Voucher', 'School Supply Kit', 'Emergency Shelter',
                'Health Screening', 'Tax Preparation', 'GED Tutoring',
                'Budget Counseling', 'Prescription Assistance',
            ]),
            'code' => fake()->optional(0.7)->bothify('SVC-###'),
            'description' => fake()->sentence(),
            'unit_of_measure' => fake()->randomElement(['instance', 'hour', 'dollar', 'item']),
            'is_active' => true,
        ];
    }
}
