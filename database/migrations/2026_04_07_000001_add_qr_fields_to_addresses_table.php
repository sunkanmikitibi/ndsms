<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Add QR code field if not exists
            if (!Schema::hasColumn('addresses', 'qr_code')) {
                $table->string('qr_code')->nullable()->unique();
            }

            // Add verification tracking fields if not exists
            if (!Schema::hasColumn('addresses', 'last_verified_at')) {
                $table->timestamp('last_verified_at')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'verified_by_id')) {
                $table->foreignId('verified_by_id')->nullable()->constrained('users')->onDelete('set null');
            }

            // Add code field if not exists (for fallback searching)
            if (!Schema::hasColumn('addresses', 'code')) {
                $table->string('code')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'qr_code')) {
                $table->dropUnique(['qr_code']);
                $table->dropColumn('qr_code');
            }

            if (Schema::hasColumn('addresses', 'last_verified_at')) {
                $table->dropColumn('last_verified_at');
            }

            if (Schema::hasColumn('addresses', 'verified_by_id')) {
                $table->dropForeign(['verified_by_id']);
                $table->dropColumn('verified_by_id');
            }

            if (Schema::hasColumn('addresses', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
