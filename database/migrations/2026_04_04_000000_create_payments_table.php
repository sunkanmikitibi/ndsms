<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('street_application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('NGN');
            $table->string('payment_method'); // paystack, flutterwave, bank_transfer
            $table->string('status')->default('pending'); // pending, completed, failed, cancelled
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
