<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // info, success, warning, error, approval, rejection, payment, delivery
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('action_url')->nullable(); // Link to related resource
            $table->string('action_label')->nullable(); // Button text
            $table->json('metadata')->nullable(); // Additional data like reference_number, user_name, etc.
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
