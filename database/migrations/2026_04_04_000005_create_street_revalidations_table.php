<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('street_revalidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('street_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('street_name');
            $table->string('ward');
            $table->text('reason')->nullable();
            $table->json('supporting_documents')->nullable(); // Array of document paths/URLs
            $table->string('current_status')->nullable(); // Status of the street being revalidated
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('street_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('street_revalidations');
    }
};
