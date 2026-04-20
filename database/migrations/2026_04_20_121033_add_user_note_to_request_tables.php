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
        $tables = [
            'street_applications' => 'admin_note',
            'addresses' => 'admin_note',
            'address_indexing_requests' => 'admin_note',
            'street_revalidations' => 'admin_note',
            'street_numbering_plates' => 'admin_notes',
            'field_reports' => 'admin_note'
        ];

        foreach ($tables as $table => $after) {
            Schema::table($table, function (Blueprint $table) use ($after) {
                $table->text('user_note')->nullable()->after($after);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'street_applications',
            'addresses',
            'address_indexing_requests',
            'street_revalidations',
            'street_numbering_plates',
            'field_reports'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('user_note');
            });
        }
    }
};
