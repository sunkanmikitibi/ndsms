<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->decimal('distance', 10, 2)->nullable(); // Distance in meters
        });

        Schema::table('street_applications', function (Blueprint $table) {
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->decimal('distance', 10, 2)->nullable(); // Distance in meters
        });
    }

    public function down(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->dropColumn(['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'distance']);
        });

        Schema::table('street_applications', function (Blueprint $table) {
            $table->dropColumn(['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'distance']);
        });
    }
};
