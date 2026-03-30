<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->string('county')->nullable();
            $table->string('housing_type')->nullable(); // own, rent, homeless, transitional, other
            $table->unsignedSmallInteger('household_size')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('zip');
            $table->index('county');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
