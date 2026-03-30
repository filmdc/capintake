<?php

declare(strict_types=1);

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caseworker_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default(EnrollmentStatus::Pending->value);
            $table->date('enrolled_at');
            $table->date('completed_at')->nullable();
            $table->decimal('household_income_at_enrollment', 10, 2)->nullable();
            $table->unsignedSmallInteger('household_size_at_enrollment')->nullable();
            $table->unsignedSmallInteger('fpl_percent_at_enrollment')->nullable(); // calculated % of FPL
            $table->boolean('income_eligible')->default(false);
            $table->text('eligibility_notes')->nullable();
            $table->text('denial_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('program_id');
            $table->index('caseworker_id');
            $table->index('status');
            $table->index('enrolled_at');
            $table->unique(['client_id', 'program_id', 'enrolled_at']); // prevent duplicate enrollments same day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
