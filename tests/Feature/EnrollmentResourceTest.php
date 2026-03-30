<?php

declare(strict_types=1);

use App\Enums\EnrollmentStatus;
use App\Filament\Resources\EnrollmentResource\Pages\CreateEnrollment;
use App\Filament\Resources\EnrollmentResource\Pages\EditEnrollment;
use App\Filament\Resources\EnrollmentResource\Pages\ListEnrollments;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Program;
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

it('can list enrollments for an authenticated admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ListEnrollments::class)
        ->assertSuccessful();
});

it('can list enrollments for a caseworker', function () {
    $this->actingAs($this->caseworker);

    Livewire::test(ListEnrollments::class)
        ->assertSuccessful();
});

// --- Create ---

it('can create an enrollment with valid data', function () {
    $client = Client::factory()->create();
    $program = Program::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(CreateEnrollment::class)
        ->fillForm([
            'client_id' => $client->id,
            'program_id' => $program->id,
            'caseworker_id' => $this->caseworker->id,
            'status' => EnrollmentStatus::Active->value,
            'enrolled_at' => '2025-06-01',
            'income_eligible' => true,
            'household_income_at_enrollment' => 25000,
            'household_size_at_enrollment' => 3,
            'fpl_percent_at_enrollment' => 125,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('enrollments', [
        'client_id' => $client->id,
        'program_id' => $program->id,
        'caseworker_id' => $this->caseworker->id,
        'status' => EnrollmentStatus::Active->value,
    ]);
});

it('caseworker can create an enrollment', function () {
    $client = Client::factory()->create();
    $program = Program::factory()->create();

    $this->actingAs($this->caseworker);

    Livewire::test(CreateEnrollment::class)
        ->fillForm([
            'client_id' => $client->id,
            'program_id' => $program->id,
            'caseworker_id' => $this->caseworker->id,
            'status' => EnrollmentStatus::Pending->value,
            'enrolled_at' => '2025-07-01',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('enrollments', [
        'client_id' => $client->id,
        'program_id' => $program->id,
    ]);
});

// --- Validation ---

it('cannot create an enrollment with missing required fields', function () {
    $this->actingAs($this->admin);

    Livewire::test(CreateEnrollment::class)
        ->fillForm([
            'client_id' => null,
            'program_id' => null,
            'caseworker_id' => null,
            'status' => null,
            'enrolled_at' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'client_id' => 'required',
            'program_id' => 'required',
            'caseworker_id' => 'required',
            'status' => 'required',
            'enrolled_at' => 'required',
        ]);
});

// --- Edit ---

it('can edit an existing enrollment as admin', function () {
    $enrollment = Enrollment::factory()->create([
        'status' => EnrollmentStatus::Pending,
    ]);

    $this->actingAs($this->admin);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->fillForm([
            'status' => EnrollmentStatus::Active->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('enrollments', [
        'id' => $enrollment->id,
        'status' => EnrollmentStatus::Active->value,
    ]);
});

it('caseworker can edit their own enrollment', function () {
    $enrollment = Enrollment::factory()->create([
        'caseworker_id' => $this->caseworker->id,
        'status' => EnrollmentStatus::Pending,
    ]);

    $this->actingAs($this->caseworker);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->fillForm([
            'status' => EnrollmentStatus::Active->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('enrollments', [
        'id' => $enrollment->id,
        'status' => EnrollmentStatus::Active->value,
    ]);
});

// --- Delete (soft delete) ---

it('admin can delete an enrollment', function () {
    $enrollment = Enrollment::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted('enrollments', ['id' => $enrollment->id]);
});

// --- Authorization ---

it('caseworker cannot edit enrollment belonging to another caseworker', function () {
    $otherCaseworker = User::factory()->caseworker()->create();
    $enrollment = Enrollment::factory()->create([
        'caseworker_id' => $otherCaseworker->id,
    ]);

    $this->actingAs($this->caseworker);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->assertForbidden();
});

it('caseworker cannot delete an enrollment', function () {
    $enrollment = Enrollment::factory()->create([
        'caseworker_id' => $this->caseworker->id,
    ]);

    $this->actingAs($this->caseworker);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->assertActionHidden(DeleteAction::class);
});

it('supervisor can delete an enrollment', function () {
    $enrollment = Enrollment::factory()->create();

    $this->actingAs($this->supervisor);

    Livewire::test(EditEnrollment::class, ['record' => $enrollment->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted('enrollments', ['id' => $enrollment->id]);
});
