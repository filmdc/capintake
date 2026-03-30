<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRecordFactory extends Factory
{
    protected $model = ServiceRecord::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'service_id' => Service::factory(),
            'enrollment_id' => Enrollment::factory(),
            'provided_by' => User::factory()->caseworker(),
            'service_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'quantity' => fake()->randomFloat(2, 1, 10),
            'value' => fake()->optional(0.5)->randomFloat(2, 25, 500),
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }
}
