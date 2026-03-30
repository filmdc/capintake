<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IncomeFrequency;
use App\Models\Client;
use App\Models\IncomeRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeRecordFactory extends Factory
{
    protected $model = IncomeRecord::class;

    public function definition(): array
    {
        $frequency = fake()->randomElement(IncomeFrequency::cases());
        $amount = fake()->randomFloat(2, 200, 3000);

        return [
            'client_id' => Client::factory(),
            'household_member_id' => null,
            'source' => fake()->randomElement([
                'employment', 'ssi', 'ssdi', 'tanf', 'snap',
                'child_support', 'pension', 'unemployment',
                'self_employment', 'other',
            ]),
            'source_description' => fake()->optional(0.5)->company(),
            'amount' => $amount,
            'frequency' => $frequency,
            'annual_amount' => round($amount * $frequency->annualMultiplier(), 2),
            'is_verified' => fake()->boolean(60),
            'verification_method' => fake()->randomElement(['pay_stub', 'tax_return', 'benefit_letter', 'self_declaration']),
            'verified_at' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'effective_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expiration_date' => fake()->optional(0.3)->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
            'verification_method' => 'pay_stub',
        ]);
    }

    public function employment(): static
    {
        return $this->state(fn () => [
            'source' => 'employment',
            'frequency' => IncomeFrequency::Biweekly,
            'source_description' => fake()->company(),
        ]);
    }

    public function ssi(): static
    {
        return $this->state(fn () => [
            'source' => 'ssi',
            'frequency' => IncomeFrequency::Monthly,
            'amount' => 943.00, // 2025 max SSI
            'annual_amount' => 11316.00,
        ]);
    }
}
