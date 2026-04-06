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
            // Basic contact information
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('town')->nullable()->after('address');
            
            // Organization information
            $table->string('organization')->nullable()->after('town');
            $table->string('position')->nullable()->after('organization');
            $table->string('department')->nullable()->after('position');
            
            // Additional information
            $table->string('state')->nullable()->after('department');
            $table->string('country')->nullable()->after('state');
            $table->string('avatar')->nullable()->after('country');
            $table->boolean('is_super_admin')->default(false)->after('avatar');
            
            // Indexes for faster queries
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'phone',
                'address',
                'town',
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
