<?php

declare(strict_types=1);

use App\Filament\Pages\NpiReport;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Services\NpiReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/**
 * Helper: seed NPI goals, programs, services, and their mappings.
 */
function seedNpiData(): void
{
    test()->seed(\Database\Seeders\NpiSeeder::class);
    test()->seed(\Database\Seeders\ProgramSeeder::class);
    test()->seed(\Database\Seeders\NpiServiceMappingSeeder::class);
}

/**
 * Helper: create a service record for a seeded service code.
 */
function createServiceRecordForCode(
    string $serviceCode,
    ?Client $client = null,
    ?string $serviceDate = null,
    float $value = 100.00,
): ServiceRecord {
    $service = Service::where('code', $serviceCode)->firstOrFail();
    $program = $service->program;
    $caseworker = User::factory()->caseworker()->create();
    $client ??= Client::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'client_id' => $client->id,
        'program_id' => $program->id,
        'caseworker_id' => $caseworker->id,
    ]);

    return ServiceRecord::factory()->create([
        'client_id' => $client->id,
        'service_id' => $service->id,
        'enrollment_id' => $enrollment->id,
        'provided_by' => $caseworker->id,
        'service_date' => $serviceDate ?? now()->format('Y-m-d'),
        'value' => $value,
    ]);
}

// =============================================================================
// NpiReportService
// =============================================================================

it('generate returns data for all 7 goals', function () {
    seedNpiData();

    $service = new NpiReportService();
    $report = $service->generate('2025-01-01', '2026-12-31');

    expect($report)->toHaveCount(7);

    $goalNumbers = $report->pluck('goal_number')->toArray();
    expect($goalNumbers)->toBe([1, 2, 3, 4, 5, 6, 7]);
});

it('unduplicated count is correct — same client with 2 service records for same indicator counts as 1', function () {
    seedNpiData();

    // CSBG-VITA maps to indicator 3.1
    $client = Client::factory()->create();
    createServiceRecordForCode('CSBG-VITA', $client, '2025-06-01', 50.00);
    createServiceRecordForCode('CSBG-VITA', $client, '2025-06-15', 75.00);

    $service = new NpiReportService();
    $report = $service->generate('2025-01-01', '2025-12-31');

    // Find Goal 3 (Income and Asset Building)
    $goal3 = $report->firstWhere('goal_number', 3);
    expect($goal3)->not->toBeNull();

    // Find indicator 3.1
    $indicator31 = collect($goal3['indicators'])->firstWhere('indicator_code', '3.1');
    expect($indicator31)->not->toBeNull();
    expect($indicator31['unduplicated_clients'])->toBe(1);
    expect($indicator31['total_services'])->toBe(2);
});

it('service records outside date range are excluded', function () {
    seedNpiData();

    $client = Client::factory()->create();
    // Inside range
    createServiceRecordForCode('CSBG-VITA', $client, '2025-06-01', 100.00);
    // Outside range
    createServiceRecordForCode('CSBG-VITA', $client, '2024-01-01', 200.00);

    $service = new NpiReportService();
    $report = $service->generate('2025-01-01', '2025-12-31');

    $goal3 = $report->firstWhere('goal_number', 3);
    $indicator31 = collect($goal3['indicators'])->firstWhere('indicator_code', '3.1');

    expect($indicator31['unduplicated_clients'])->toBe(1);
    expect($indicator31['total_services'])->toBe(1);
    expect($indicator31['total_value'])->toBe(100.00);
});

it('goal-level unduplicated count works — client with services under 2 indicators in same goal counts once', function () {
    seedNpiData();

    // CSBG-VITA maps to 3.1, CSBG-IR maps to 3.3 — both under Goal 3
    $client = Client::factory()->create();
    createServiceRecordForCode('CSBG-VITA', $client, '2025-06-01');
    createServiceRecordForCode('CSBG-IR', $client, '2025-06-15');

    $service = new NpiReportService();
    $report = $service->generate('2025-01-01', '2025-12-31');

    $goal3 = $report->firstWhere('goal_number', 3);
    expect($goal3['goal_total_clients'])->toBe(1);
});

it('grandTotalUnduplicatedClients returns correct count', function () {
    seedNpiData();

    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    // Client A gets two different services (different goals)
    createServiceRecordForCode('CSBG-VITA', $clientA, '2025-06-01');
    createServiceRecordForCode('EMRG-FOOD', $clientA, '2025-06-15');

    // Client B gets one service
    createServiceRecordForCode('EMRG-RENT', $clientB, '2025-07-01');

    $service = new NpiReportService();
    $grandTotal = $service->grandTotalUnduplicatedClients('2025-01-01', '2025-12-31');

    expect($grandTotal)->toBe(2);
});

it('toFlatRows returns header row + goal rows + indicator rows + grand total', function () {
    seedNpiData();

    // Create at least one service record so the report has data
    createServiceRecordForCode('CSBG-VITA', serviceDate: '2025-06-01');

    $service = new NpiReportService();
    $rows = $service->toFlatRows('2025-01-01', '2025-12-31');

    // First row is the header
    expect($rows[0])->toBe([
        'NPI Code',
        'Goal / Indicator',
        'Unduplicated Individuals',
        'Total Services',
        'Total Value ($)',
    ]);

    // Last row is the grand total
    $lastRow = end($rows);
    expect($lastRow[0])->toBe('');
    expect($lastRow[1])->toBe('GRAND TOTAL (Unduplicated)');

    // Count: 1 header + 7 goal rows + 27 indicator rows + 1 grand total = 36
    // NPI seeder creates: Goal 1 (3), Goal 2 (4), Goal 3 (4), Goal 4 (3),
    // Goal 5 (4), Goal 6 (3), Goal 7 (6) = 27 indicators
    expect($rows)->toHaveCount(36);
});

// =============================================================================
// NPI Report Page
// =============================================================================

it('NPI report page renders for authenticated admin', function () {
    seedNpiData();

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(NpiReport::class)
        ->assertSuccessful();
});

it('generateReport sets reportData and grandTotal', function () {
    seedNpiData();

    $client = Client::factory()->create();
    createServiceRecordForCode('CSBG-VITA', $client, '2025-06-01');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(NpiReport::class)
        ->set('startDate', '2025-01-01')
        ->set('endDate', '2025-12-31')
        ->call('generateReport')
        ->assertSet('grandTotal', 1)
        ->assertNotSet('reportData', null);
});

it('applyPreset fiscal_year sets correct date range', function () {
    seedNpiData();

    $this->actingAs(User::factory()->admin()->create());

    $now = now();
    $expectedStart = ($now->month >= 10
        ? $now->copy()->startOfYear()->addMonths(9)
        : $now->copy()->subYear()->startOfYear()->addMonths(9))
        ->startOfMonth()
        ->format('Y-m-d');
    $expectedEnd = $now->format('Y-m-d');

    $component = Livewire::test(NpiReport::class)
        ->call('applyPreset', 'fiscal_year');

    $component->assertSet('startDate', $expectedStart);
    $component->assertSet('endDate', $expectedEnd);
});

it('applyPreset this_month sets correct date range', function () {
    seedNpiData();

    $this->actingAs(User::factory()->admin()->create());

    $now = now();
    $expectedStart = $now->copy()->startOfMonth()->format('Y-m-d');
    $expectedEnd = $now->format('Y-m-d');

    $component = Livewire::test(NpiReport::class)
        ->call('applyPreset', 'this_month');

    $component->assertSet('startDate', $expectedStart);
    $component->assertSet('endDate', $expectedEnd);
});
