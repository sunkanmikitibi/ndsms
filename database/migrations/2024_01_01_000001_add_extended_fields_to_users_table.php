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
            // Add phone and town if they don't exist (in case of fresh migration)
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'town')) {
                $table->string('town')->nullable()->after('phone');
            }
            
            // Additional contact information
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('town');
            }
            
            // Organization information
            if (!Schema::hasColumn('users', 'organization')) {
                $table->string('organization')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('organization');
            }
            
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('position');
            }
            
            // Additional location information
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('state');
            }
            
            // Profile information
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('country');
            }
            
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'town',
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

