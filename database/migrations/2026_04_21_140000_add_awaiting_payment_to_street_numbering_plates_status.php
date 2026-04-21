<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE `street_numbering_plates`
            MODIFY COLUMN `status` ENUM(
                'pending',
                'approved',
                'rejected',
                'awaiting_payment',
                'in_production',
                'ready',
                'delivered',
                'installed',
                'completed'
            ) NOT NULL DEFAULT 'pending'
        SQL);
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(<<<'SQL'
            UPDATE `street_numbering_plates`
            SET `status` = 'pending'
            WHERE `status` = 'awaiting_payment'
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE `street_numbering_plates`
            MODIFY COLUMN `status` ENUM(
                'pending',
                'approved',
                'rejected',
                'in_production',
                'ready',
                'delivered',
                'installed',
                'completed'
            ) NOT NULL DEFAULT 'pending'
        SQL);
    }
};

