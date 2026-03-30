<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provided_by')->constrained('users')->cascadeOnDelete();
            $table->date('service_date');
            $table->decimal('quantity', 10, 2)->default(1); // units of service
            $table->decimal('value', 10, 2)->nullable(); // dollar value if applicable
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('service_id');
            $table->index('enrollment_id');
            $table->index('provided_by');
            $table->index('service_date');

            // Composite index for NPI reporting queries
            $table->index(['service_id', 'service_date', 'client_id'], 'idx_npi_reporting');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
