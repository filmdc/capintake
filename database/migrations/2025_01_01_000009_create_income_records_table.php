<?php

declare(strict_types=1);

use App\Enums\IncomeFrequency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_records', function (Blueprint $table) {
            $table->id();
            // Income can belong to either the client or a household member
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('household_member_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('source'); // employment, ssi, ssdi, tanf, snap, child_support, pension, unemployment, self_employment, other
            $table->string('source_description')->nullable(); // employer name or detail
            $table->decimal('amount', 10, 2);
            $table->string('frequency')->default(IncomeFrequency::Monthly->value);
            $table->decimal('annual_amount', 10, 2)->nullable(); // auto-calculated
            $table->boolean('is_verified')->default(false);
            $table->string('verification_method')->nullable(); // pay_stub, tax_return, benefit_letter, self_declaration
            $table->date('verified_at')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('household_member_id');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_records');
    }
};
