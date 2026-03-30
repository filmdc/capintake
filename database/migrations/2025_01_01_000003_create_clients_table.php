<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->text('ssn_encrypted')->nullable(); // encrypted at rest
            $table->string('ssn_last_four', 4)->nullable(); // for quick lookup
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable(); // male, female, non_binary, other, prefer_not_to_say
            $table->string('race')->nullable(); // HUD categories
            $table->string('ethnicity')->nullable(); // hispanic_latino, not_hispanic_latino
            $table->boolean('is_veteran')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->boolean('is_head_of_household')->default(false);
            $table->string('preferred_language')->default('en');
            $table->string('relationship_to_head')->nullable(); // self, spouse, child, parent, other
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['last_name', 'first_name']);
            $table->index('date_of_birth');
            $table->index('ssn_last_four');
            $table->index('household_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
