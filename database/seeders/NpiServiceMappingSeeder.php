<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NpiIndicator;
use App\Models\Service;
use Illuminate\Database\Seeder;

class NpiServiceMappingSeeder extends Seeder
{
    /**
     * Map service codes to NPI indicator codes.
     * A single service can map to multiple indicators.
     */
    public function run(): void
    {
        $mappings = [
            // Employment services → Goal 1
            'CSBG-ERT' => ['1.1', '1.2'],

            // Education services → Goal 2
            'CSBG-FLW' => ['2.2', '3.4'],

            // Income & Asset Building → Goal 3
            'CSBG-VITA' => ['3.1'],
            'CSBG-IR' => ['3.3'],
            'CSBG-CM' => ['1.2', '3.3', '4.2'],

            // Housing → Goal 4
            'EMRG-RENT' => ['4.1', '4.3', '7.4'],
            'WAP-AUDIT' => ['4.2'],
            'WAP-INS' => ['4.2'],
            'WAP-FURN' => ['4.2'],
            'WAP-SEAL' => ['4.2'],
            'WAP-WIN' => ['4.2'],

            // Health → Goal 5
            'EMRG-RX' => ['5.1', '7.5'],

            // Emergency / Multi-domain → Goal 7
            'EMRG-FOOD' => ['7.1', '7.2'],
            'EMRG-UTIL' => ['7.1', '7.3'],
            'EMRG-CLO' => ['7.1'],
        ];

        foreach ($mappings as $serviceCode => $indicatorCodes) {
            $service = Service::where('code', $serviceCode)->first();
            if (! $service) {
                continue;
            }

            $indicatorIds = NpiIndicator::whereIn('indicator_code', $indicatorCodes)
                ->pluck('id')
                ->toArray();

            $service->npiIndicators()->syncWithoutDetaching($indicatorIds);
        }
    }
}
