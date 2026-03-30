<?php

declare(strict_types=1);

use App\Filament\Resources\ServiceRecordResource\Pages\CreateServiceRecord;
use App\Filament\Resources\ServiceRecordResource\Pages\EditServiceRecord;
use App\Filament\Resources\ServiceRecordResource\Pages\ListServiceRecords;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->supervisor = User::factory()->supervisor()->create();
    $this->caseworker = User::factory()->caseworker()->create();
});

// --- List ---

it('can list service records for an authenticated admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ListServiceRecords::class)
        ->assertSuccessful();
});

it('can list service records for a caseworker', function () {
    $this->actingAs($this->caseworker);

    Livewire::test(ListServiceRecords::class)
        ->assertSuccessful();
});

// --- Create ---

it('can create a service record with valid data', function () {
    $client = Client::factory()->create();
    $service = Service::factory()->create();
    $enrollment = Enrollment::factory()->create(['client_id' => $client->id]);

    $this->actingAs($this->admin);

    Livewire::test(CreateServiceRecord::class)
        ->fillForm([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'enrollment_id' => $enrollment->id,
            'provided_by' => $this->caseworker->id,
            'service_date' => '2025-08-15',
            'quantity' => 2.50,
            'value' => 150.00,
            'notes' => 'Initial service delivery.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('service_records', [
        'client_id' => $client->id,
        'service_id' => $service->id,
        'provided_by' => $this->caseworker->id,
    ]);
});

it('caseworker can create a service record', function () {
    $client = Client::factory()->create();
    $service = Service::factory()->create();
    $enrollment = Enrollment::factory()->create(['client_id' => $client->id]);

    $this->actingAs($this->caseworker);

    Livewire::test(CreateServiceRecord::class)
        ->fillForm([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'enrollment_id' => $enrollment->id,
            'provided_by' => $this->caseworker->id,
            'service_date' => '2025-09-01',
            'quantity' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('service_records', [
        'client_id' => $client->id,
        'service_id' => $service->id,
    ]);
});

// --- Validation ---

it('cannot create a service record with missing required fields', function () {
    $this->actingAs($this->admin);

    Livewire::test(CreateServiceRecord::class)
        ->fillForm([
            'client_id' => null,
            'service_id' => null,
            'provided_by' => null,
            'service_date' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'client_id' => 'required',
            'service_id' => 'required',
            'provided_by' => 'required',
            'service_date' => 'required',
        ]);
});

// --- Edit ---

it('can edit an existing service record', function () {
    $serviceRecord = ServiceRecord::factory()->create([
        'notes' => 'Original notes',
    ]);

    $this->actingAs($this->admin);

    Livewire::test(EditServiceRecord::class, ['record' => $serviceRecord->getRouteKey()])
        ->fillForm([
            'notes' => 'Updated notes for this service.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('service_records', [
        'id' => $serviceRecord->id,
        'notes' => 'Updated notes for this service.',
    ]);
});

// --- Delete (soft delete) ---

it('admin can delete a service record', function () {
    $serviceRecord = ServiceRecord::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(EditServiceRecord::class, ['record' => $serviceRecord->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted('service_records', ['id' => $serviceRecord->id]);
});

it('supervisor can delete a service record', function () {
    $serviceRecord = ServiceRecord::factory()->create();

    $this->actingAs($this->supervisor);

    Livewire::test(EditServiceRecord::class, ['record' => $serviceRecord->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted('service_records', ['id' => $serviceRecord->id]);
});

// --- Authorization ---

it('caseworker cannot delete a service record', function () {
    $serviceRecord = ServiceRecord::factory()->create();

    $this->actingAs($this->caseworker);

    Livewire::test(EditServiceRecord::class, ['record' => $serviceRecord->getRouteKey()])
        ->assertActionHidden(DeleteAction::class);
});
