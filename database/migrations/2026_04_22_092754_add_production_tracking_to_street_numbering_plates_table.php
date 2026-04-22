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
        Schema::table('street_numbering_plates', function (Blueprint $table) {
            // Production tracking fields
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('production_started_at')->nullable();
            $table->timestamp('production_completed_at')->nullable();
            $table->date('estimated_completion_date')->nullable();
            $table->text('production_notes')->nullable();

            // Indexes
            $table->index('assigned_to');
            $table->index('production_started_at');
            $table->index('estimated_completion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('street_numbering_plates', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'assigned_to',
                'production_started_at',
                'production_completed_at',
                'estimated_completion_date',
                'production_notes',
            ]);
        });
    }
};
