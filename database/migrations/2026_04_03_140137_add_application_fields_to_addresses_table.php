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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('applicant_name')->nullable()->after('id');
            $table->string('applicant_phone')->nullable()->after('applicant_name');
            $table->string('reference_code')->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['applicant_name', 'applicant_phone', 'reference_code']);
        });
    }
};
