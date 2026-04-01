<?php

declare(strict_types=1);

use App\Filament\Pages\SetupWizard;
use App\Models\AgencySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('setup wizard page loads when setup is not complete', function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get('/admin/setup')
        ->assertSuccessful();
});

it('setup wizard redirects to dashboard when setup is already complete', function () {
    AgencySetting::create([
        'agency_name' => 'Test Agency',
        'setup_completed' => true,
    ]);

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(SetupWizard::class)
        ->assertRedirect('/admin');
});

it('other admin pages redirect to setup when setup is not complete', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get('/admin')
        ->assertRedirect('/admin/setup');
});

it('completing setup creates agency settings record', function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(SetupWizard::class)
        ->fillForm([
            'agency_name' => 'Example CAP Agency',
            'agency_city' => 'Springfield',
            'agency_state' => 'PA',
            'agency_zip' => '19064',
            'primary_color' => '#3b82f6',
            'fiscal_year_start_month' => 10,
        ])
        ->call('submit');

    $this->assertDatabaseHas('agency_settings', [
        'agency_name' => 'Example CAP Agency',
        'agency_city' => 'Springfield',
        'setup_completed' => true,
    ]);
});

it('login page is not blocked by setup middleware', function () {
    // No agency settings = setup not complete
    // Login page should still be accessible
    $this->get('/admin/login')
        ->assertSuccessful();
});

it('isSetupComplete returns false when no settings exist', function () {
    expect(AgencySetting::isSetupComplete())->toBeFalse();
});

it('isSetupComplete returns true when setup is completed', function () {
    AgencySetting::create([
        'agency_name' => 'Test',
        'setup_completed' => true,
    ]);

    expect(AgencySetting::isSetupComplete())->toBeTrue();
});

it('agency settings current method returns singleton', function () {
    AgencySetting::create([
        'agency_name' => 'Test Agency',
        'setup_completed' => true,
    ]);

    $first = AgencySetting::current();
    $second = AgencySetting::current();

    expect($first)->toBe($second);
    expect($first->agency_name)->toBe('Test Agency');
});
