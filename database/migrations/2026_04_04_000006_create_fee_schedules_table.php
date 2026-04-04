<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('service_type')->index();
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->decimal('base_amount', 12, 2);
            $table->string('currency')->default('NGN');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['service_type', 'status']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_schedules');
    }
};
