<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_settings', function (Blueprint $table) {
            $table->id();
            $table->string('agency_name');
            $table->string('agency_address_line_1')->nullable();
            $table->string('agency_address_line_2')->nullable();
            $table->string('agency_city')->nullable();
            $table->string('agency_state', 2)->nullable();
            $table->string('agency_zip', 10)->nullable();
            $table->string('agency_county')->nullable();
            $table->string('agency_phone')->nullable();
            $table->string('agency_ein')->nullable();
            $table->string('agency_website')->nullable();
            $table->string('executive_director_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#3b82f6');
            $table->unsignedSmallInteger('fiscal_year_start_month')->default(10);
            $table->boolean('setup_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_settings');
    }
};
