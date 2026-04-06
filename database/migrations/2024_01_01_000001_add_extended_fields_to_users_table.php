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
        Schema::table('users', function (Blueprint $table) {
            // Additional contact information (phone and town already exist)
            $table->string('address')->nullable()->after('town');
            
            // Organization information
            $table->string('organization')->nullable()->after('address');
            $table->string('position')->nullable()->after('organization');
            $table->string('department')->nullable()->after('position');
            
            // Additional location information
            $table->string('state')->nullable()->after('department');
            $table->string('country')->nullable()->after('state');
            
            // Profile information
            $table->string('avatar')->nullable()->after('country');
            $table->boolean('is_super_admin')->default(false)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'organization',
                'position',
                'department',
                'state',
                'country',
                'avatar',
                'is_super_admin',
            ]);
        });
    }
};
