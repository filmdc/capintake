<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FederalPovertyLevel;
use Illuminate\Database\Seeder;

class FederalPovertyLevelSeeder extends Seeder
{
    public function run(): void
    {
        // 2025 HHS Poverty Guidelines
        // Source: https://aspe.hhs.gov/topics/poverty-economic-mobility/poverty-guidelines
        $guidelines = [
            'continental' => [
                1 => 15650,
                2 => 21150,
                3 => 26650,
                4 => 32150,
                5 => 37650,
                6 => 43150,
                7 => 48650,
                8 => 54150,
                // For each additional person, add $5,500
            ],
            'alaska' => [
                1 => 19560,
                2 => 26430,
                3 => 33300,
                4 => 40170,
                5 => 47040,
                6 => 53910,
                7 => 60780,
                8 => 67650,
            ],
            'hawaii' => [
                1 => 18000,
                2 => 24330,
                3 => 30660,
                4 => 36990,
                5 => 43320,
                6 => 49650,
                7 => 55980,
                8 => 62310,
            ],
        ];

        // Seed for 2025 and 2026 (use 2025 guidelines as estimate for 2026
        // until official HHS numbers are published)
        foreach ([2025, 2026] as $year) {
            foreach ($guidelines as $region => $levels) {
                foreach ($levels as $size => $amount) {
                    FederalPovertyLevel::updateOrCreate(
                        [
                            'year' => $year,
                            'household_size' => $size,
                            'region' => $region,
                        ],
                        [
                            'poverty_guideline' => $amount,
                        ]
                    );
                }
            }
        }
    }
}
