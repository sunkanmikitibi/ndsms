<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->string('type')->default('general'); // approval, rejection, payment, otp, delivery
            $table->string('status')->default('pending'); // pending, sent, failed, error
            $table->string('message_id')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'status']);
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_notification_logs');
    }
};
