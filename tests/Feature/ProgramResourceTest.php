<?php

declare(strict_types=1);

use App\Filament\Resources\ProgramResource\Pages\CreateProgram;
use App\Filament\Resources\ProgramResource\Pages\EditProgram;
use App\Filament\Resources\ProgramResource\Pages\ListPrograms;
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

it('can list programs for an authenticated admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ListPrograms::class)
        ->assertSuccessful();
});

it('can list programs for a caseworker', function () {
    $this->actingAs($this->caseworker);

    Livewire::test(ListPrograms::class)
        ->assertSuccessful();
});

// --- Create ---

it('can create a program with valid data as admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(CreateProgram::class)
        ->fillForm([
            'name' => 'Test CSBG Program',
            'code' => 'TST01',
            'description' => 'A test program for CSBG.',
            'funding_source' => 'CSBG',
            'fiscal_year_start' => '2025-10-01',
            'fiscal_year_end' => '2026-09-30',
            'requires_income_eligibility' => true,
            'fpl_threshold_percent' => 200,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('programs', [
        'name' => 'Test CSBG Program',
        'code' => 'TST01',
        'funding_source' => 'CSBG',
        'fpl_threshold_percent' => 200,
    ]);
});

it('can create a program as supervisor', function () {
    $this->actingAs($this->supervisor);

    Livewire::test(CreateProgram::class)
        ->fillForm([
            'name' => 'Supervisor Created Program',
            'code' => 'SUP01',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('programs', [
        'name' => 'Supervisor Created Program',
        'code' => 'SUP01',
    ]);
});

// --- Validation ---

it('cannot create a program with missing required fields', function () {
    $this->actingAs($this->admin);

    Livewire::test(CreateProgram::class)
        ->fillForm([
            'name' => '',
            'code' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'code' => 'required']);
});

it('cannot create a program with a duplicate code', function () {
    Program::factory()->create(['code' => 'DUP1']);

    $this->actingAs($this->admin);

    Livewire::test(CreateProgram::class)
        ->fillForm([
            'name' => 'Duplicate Code Program',
            'code' => 'DUP1',
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});

// --- Edit ---

it('can edit an existing program as admin', function () {
    $program = Program::factory()->create(['name' => 'Original Name']);

    $this->actingAs($this->admin);

    Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Name',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('programs', [
        'id' => $program->id,
        'name' => 'Updated Name',
    ]);
});

// --- Delete (soft delete) ---

it('can delete a program as admin', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted('programs', ['id' => $program->id]);
});

// --- Authorization ---

it('prevents caseworker from creating a program', function () {
    $this->actingAs($this->caseworker);

    Livewire::test(CreateProgram::class)
        ->assertForbidden();
});

it('prevents caseworker from editing a program', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->caseworker);

    Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
        ->assertForbidden();
});

it('prevents caseworker from deleting a program', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->caseworker);

    // Caseworker cannot even access the edit page, so they can't delete
    Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
        ->assertForbidden();
});
