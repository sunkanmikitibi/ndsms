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
        Schema::table('streets', function (Blueprint $table) {
            $table->renameColumn('ward', 'town');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('ward', 'town');
        });

        Schema::table('street_applications', function (Blueprint $table) {
            $table->renameColumn('ward', 'town');
        });

        Schema::table('street_revalidations', function (Blueprint $table) {
            $table->renameColumn('ward', 'town');
        });

        Schema::table('street_numbering_plates', function (Blueprint $table) {
            $table->renameColumn('ward', 'town');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->renameColumn('town', 'ward');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('town', 'ward');
        });

        Schema::table('street_applications', function (Blueprint $table) {
            $table->renameColumn('town', 'ward');
        });

        Schema::table('street_revalidations', function (Blueprint $table) {
            $table->renameColumn('town', 'ward');
        });

        Schema::table('street_numbering_plates', function (Blueprint $table) {
            $table->renameColumn('town', 'ward');
        });
    }
};
