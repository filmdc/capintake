# CLAUDE.md — CAPIntake

## Purpose

CAPIntake is an open-source client intake and case management system for Community Action Agencies (CAPs). It replaces expensive proprietary tools (CAPTAIN, GoEngage, CaseWorthy) that cost agencies thousands per year. There is no open-source alternative — we are building the first one.

## Tech Stack

- **Framework:** Laravel 13 (PHP 8.3+, strict types everywhere)
- **Admin Panel:** Filament 4.x (all CRUD via Filament Resources)
- **Frontend:** Livewire 3 + Alpine.js (no separate SPA)
- **Database:** MySQL 8 or PostgreSQL 15 (SQLite for local dev)
- **Testing:** Pest PHP (every model gets a factory, every resource gets feature tests)
- **Reporting:** DomPDF for PDF exports, plain PHP streaming for CSV
- **Auth:** Filament's built-in auth with role-based access + FilamentUser interface

## Domain Vocabulary

Use these exact terms — they match how CAP agencies talk:

| Model             | What it represents                                                    |
|--------------------|----------------------------------------------------------------------|
| `Client`           | An individual seeking services (the primary intake record)           |
| `Household`        | A group of people living together; a Client belongs to a Household   |
| `HouseholdMember`  | A person in the household (may or may not be a Client)               |
| `Program`          | A funded program the agency runs (CSBG, Emergency Services, etc.)    |
| `Enrollment`       | A Client's enrollment in a specific Program, with eligibility status |
| `Service`          | A type of service available under a Program                          |
| `ServiceRecord`    | An actual service delivered to a Client (date, caseworker, notes)    |
| `Income`           | An income source for a HouseholdMember (amount, frequency, type)     |
| `NpiCategory`      | A National Performance Indicator goal/indicator for federal reporting |
| `Outcome`          | Links a ServiceRecord to an NpiCategory for NPI reporting            |
| `User`             | A system user: admin, supervisor, or caseworker                      |
| `AgencySetting`    | Singleton model for agency identity, branding, and fiscal year config|
| `AuditLog`         | Polymorphic record of model changes (who, what, when, old/new vals)  |

## Code Standards

- PSR-12 coding style. `declare(strict_types=1)` in every PHP file.
- Named routes for everything: `route('clients.show', $client)`.
- Form Requests for all validation — never validate in controllers or resources inline.
- All admin CRUD goes through Filament Resources. No custom controllers for admin screens.
- Encrypt PII at rest: SSN, DOB, and income fields use Laravel's `encrypted` cast.
- Soft deletes on Client, Household, HouseholdMember, Enrollment, ServiceRecord.
- Every model relationship must be explicitly defined (no implicit magic).
- Migrations must include proper indexes on foreign keys and commonly filtered columns.

## Testing Rules

- Every model MUST have a factory in `database/factories/`.
- Every Filament Resource MUST have Pest feature tests covering: list, create, edit, delete, validation errors, and authorization.
- Run `php artisan test` before every commit. Never commit with failing tests.
- Use `RefreshDatabase` trait in all feature tests.
- Test authorization: caseworkers cannot access admin-only resources.

## Three Principles

1. **Reduce cognitive load.** Every screen, form, and workflow must be simpler than what it replaces. If an intake takes more than 10 minutes, we failed. Fewer fields per step. Smart defaults. No jargon the caseworker doesn't use.

2. **Keep changes simple.** Small PRs. One concern per commit. If a change touches more than 5 files, ask whether it should be split. Prefer boring, readable code over clever abstractions.

3. **Fix root causes.** When something breaks, find out why — don't patch symptoms. If a test is flaky, fix the test setup, don't skip it. If a form is confusing, redesign the flow, don't add a tooltip.

## What NOT To Do

- **Don't over-engineer.** No event sourcing, no microservices, no GraphQL. Laravel's built-in tools handle everything we need.
- **Don't add features outside the current sprint.** No "while I'm here" additions. If it's not in scope, open an issue and move on.
- **Don't break existing tests.** If your change breaks a test, fix your change — not the test — unless the test is genuinely wrong.
- **Don't use `$table->string()` for sensitive fields.** SSN and similar PII must use encrypted storage with the `encrypted` cast on the model.
- **Don't create Filament Resources without an authorization Policy.** Every resource needs a policy. No exceptions.
- **Don't skip the factory.** If you create a model, create its factory in the same commit.
- **Don't build a separate frontend.** Everything goes through Filament and Livewire. No React, no Vue, no Inertia.
- **Don't log SSN, password, or remember_token in audit logs.** The Auditable trait excludes these — never add them back.
- **Don't hardcode agency name or branding.** Use `AgencySetting::current()` for agency identity values.

## Mistakes to Avoid (Lessons Learned)

- **Filament 4 Wizard `afterValidation` + `Halt` doesn't fully prevent step advancement.** The step index is incremented before the try/catch. Use reactive `live()` fields for inline validation instead of halt-based step blocking.
- **The `IntakeWizard.php` is large (1300+ lines).** If adding new steps, consider extracting step logic into dedicated classes.
- **Filament 4 action imports differ from v3.** Use `\Filament\Actions\CreateAction` (not `Filament\Tables\Actions\CreateAction`). Same for EditAction, DeleteAction, ViewAction.
- **Filament 4 property types are strict.** `$navigationGroup` must be `string|\UnitEnum|null`. `$view` is instance, not static. Check parent class signatures.
- **SQLite BETWEEN comparisons with datetimes.** `BETWEEN '2026-03-30' AND '2026-03-30'` excludes `'2026-03-30 00:00:00'` because the datetime string sorts after the date string. Use `endOfDay()` or append `' 23:59:59'`.
- **User model must implement `FilamentUser` interface.** Without it, `APP_ENV=testing` or production environments return 403 for all authenticated users. Existing local dev hides this bug.
- **Heroicon Blade components in custom widget views render without size constraints.** Use inline SVG with explicit `style="width:1.25rem;height:1.25rem"` in custom Livewire views.
