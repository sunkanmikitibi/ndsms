<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('house_number');
            $table->foreignId('street_id')->constrained()->cascadeOnDelete();
            $table->string('ward');
            $table->string('owner_name');
            $table->string('owner_phone')->nullable();
            $table->string('status')->default('active'); // active, inactive, pending
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
