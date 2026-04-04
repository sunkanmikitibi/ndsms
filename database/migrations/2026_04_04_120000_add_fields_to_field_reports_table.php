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
        Schema::table('field_reports', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->string('description')->nullable()->after('title');
            $table->string('location')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_reports', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'location']);
        });
    }
};
