<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npi_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('goal_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('npi_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npi_goal_id')->constrained()->cascadeOnDelete();
            $table->string('indicator_code')->unique(); // e.g., "1.1", "1.2", "2.1"
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('npi_goal_id');
        });

        // Pivot: which services map to which NPI indicators
        Schema::create('npi_indicator_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npi_indicator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['npi_indicator_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npi_indicator_service');
        Schema::dropIfExists('npi_indicators');
        Schema::dropIfExists('npi_goals');
    }
};
