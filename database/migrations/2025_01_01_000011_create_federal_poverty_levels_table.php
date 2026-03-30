<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('federal_poverty_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('household_size');
            $table->unsignedInteger('poverty_guideline'); // annual income in dollars
            $table->string('region')->default('continental'); // continental, alaska, hawaii
            $table->timestamps();

            $table->unique(['year', 'household_size', 'region']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('federal_poverty_levels');
    }
};
