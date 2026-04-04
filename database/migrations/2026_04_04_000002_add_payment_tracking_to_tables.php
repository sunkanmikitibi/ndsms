<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add payment tracking columns to addresses if not exists
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('reference_code');
                $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();
            }
        });

        // Add payment tracking columns to street_applications if not exists
        Schema::table('street_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('street_applications', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('reviewed_at');
                $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'payment_id')) {
                $table->dropForeignIdFor('Payment');
                $table->dropColumn('payment_id');
            }
        });

        Schema::table('street_applications', function (Blueprint $table) {
            if (Schema::hasColumn('street_applications', 'payment_id')) {
                $table->dropForeignIdFor('Payment');
                $table->dropColumn('payment_id');
            }
        });
    }
};
