<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NpiGoal;
use App\Models\NpiIndicator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NpiReportService
{
    /**
     * Generate the full NPI report for a date range.
     *
     * Returns a collection of goals, each with its indicators and counts:
     * [
     *     'goal_number' => 1,
     *     'goal_name' => 'Employment',
     *     'indicators' => [
     *         [
     *             'indicator_code' => '1.1',
     *             'indicator_name' => 'Unemployed and obtained employment',
     *             'unduplicated_clients' => 42,
     *             'total_services' => 87,
     *             'total_value' => 12500.00,
     *         ],
     *     ],
     *     'goal_total_clients' => 58,  // unduplicated across all indicators in this goal
     * ]
     */
    public function generate(string $startDate, string $endDate): Collection
    {
        // Single query: join goals → indicators → pivot → services → service_records
        $rows = DB::table('npi_goals as ng')
            ->join('npi_indicators as ni', 'ni.npi_goal_id', '=', 'ng.id')
            ->leftJoin('npi_indicator_service as nis', 'nis.npi_indicator_id', '=', 'ni.id')
            ->leftJoin('services as s', function ($join): void {
                $join->on('s.id', '=', 'nis.service_id')
                    ->whereNull('s.deleted_at');
            })
            ->leftJoin('service_records as sr', function ($join) use ($startDate, $endDate): void {
                $join->on('sr.service_id', '=', 's.id')
                    ->whereBetween('sr.service_date', [$startDate, $endDate])
                    ->whereNull('sr.deleted_at');
            })
            ->select([
                'ng.id as goal_id',
                'ng.goal_number',
                'ng.name as goal_name',
                'ni.id as indicator_id',
                'ni.indicator_code',
                'ni.name as indicator_name',
                DB::raw('COUNT(DISTINCT sr.client_id) as unduplicated_clients'),
                DB::raw('COUNT(sr.id) as total_services'),
                DB::raw('COALESCE(SUM(sr.value), 0) as total_value'),
            ])
            ->groupBy('ng.id', 'ng.goal_number', 'ng.name', 'ni.id', 'ni.indicator_code', 'ni.name')
            ->orderBy('ng.goal_number')
            ->orderBy('ni.indicator_code')
            ->get();

        // Group by goal, then compute goal-level unduplicated count
        return $rows->groupBy('goal_id')->map(function (Collection $indicators) use ($startDate, $endDate): array {
            $first = $indicators->first();

            // Goal-level unduplicated client count (a client counted once per goal,
            // even if they received services under multiple indicators in that goal)
            $goalClientCount = $this->goalUnduplicatedCount(
                (int) $first->goal_id,
                $startDate,
                $endDate,
            );

            return [
                'goal_number' => $first->goal_number,
                'goal_name' => $first->goal_name,
                'goal_total_clients' => $goalClientCount,
                'indicators' => $indicators->map(fn ($row): array => [
                    'indicator_code' => $row->indicator_code,
                    'indicator_name' => $row->indicator_name,
                    'unduplicated_clients' => (int) $row->unduplicated_clients,
                    'total_services' => (int) $row->total_services,
                    'total_value' => (float) $row->total_value,
                ])->values()->toArray(),
            ];
        })->values();
    }

    /**
     * Unduplicated client count for an entire goal (across all its indicators).
     */
    protected function goalUnduplicatedCount(int $goalId, string $startDate, string $endDate): int
    {
        return (int) DB::table('service_records as sr')
            ->join('services as s', function ($join): void {
                $join->on('s.id', '=', 'sr.service_id')
                    ->whereNull('s.deleted_at');
            })
            ->join('npi_indicator_service as nis', 'nis.service_id', '=', 's.id')
            ->join('npi_indicators as ni', function ($join) use ($goalId): void {
                $join->on('ni.id', '=', 'nis.npi_indicator_id')
                    ->where('ni.npi_goal_id', '=', $goalId);
            })
            ->whereBetween('sr.service_date', [$startDate, $endDate])
            ->whereNull('sr.deleted_at')
            ->distinct()
            ->count('sr.client_id');
    }

    /**
     * Grand total: unduplicated clients across ALL NPI indicators.
     */
    public function grandTotalUnduplicatedClients(string $startDate, string $endDate): int
    {
        return (int) DB::table('service_records as sr')
            ->join('services as s', function ($join): void {
                $join->on('s.id', '=', 'sr.service_id')
                    ->whereNull('s.deleted_at');
            })
            ->join('npi_indicator_service as nis', 'nis.service_id', '=', 's.id')
            ->whereBetween('sr.service_date', [$startDate, $endDate])
            ->whereNull('sr.deleted_at')
            ->distinct()
            ->count('sr.client_id');
    }

    /**
     * Flatten the report into rows for CSV export.
     */
    public function toFlatRows(string $startDate, string $endDate): array
    {
        $report = $this->generate($startDate, $endDate);
        $rows = [];

        $rows[] = [
            'NPI Code',
            'Goal / Indicator',
            'Unduplicated Individuals',
            'Total Services',
            'Total Value ($)',
        ];

        foreach ($report as $goal) {
            // Goal summary row
            $rows[] = [
                'Goal ' . $goal['goal_number'],
                $goal['goal_name'],
                $goal['goal_total_clients'],
                '',
                '',
            ];

            foreach ($goal['indicators'] as $indicator) {
                $rows[] = [
                    $indicator['indicator_code'],
                    $indicator['indicator_name'],
                    $indicator['unduplicated_clients'],
                    $indicator['total_services'],
                    number_format($indicator['total_value'], 2),
                ];
            }
        }

        // Grand total
        $rows[] = [
            '',
            'GRAND TOTAL (Unduplicated)',
            $this->grandTotalUnduplicatedClients($startDate, $endDate),
            '',
            '',
        ];

        return $rows;
    }
}
