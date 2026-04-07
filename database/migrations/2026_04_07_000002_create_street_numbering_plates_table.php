<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('street_numbering_plates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('street_id')->nullable()->constrained()->onDelete('set null');
            $table->string('street_name');
            $table->string('ward', 100);
            
            // Plate Specifications
            $table->integer('quantity_requested')->default(1);
            $table->enum('plate_type', ['standard', 'reflective', 'illuminated', 'digital'])->default('standard');
            $table->enum('material', ['aluminum', 'steel', 'stainless', 'plastic', 'composite'])->default('aluminum');
            $table->string('design_variant')->nullable();
            
            // Installation & Delivery
            $table->date('installation_date_requested')->nullable();
            $table->text('installation_address')->nullable();
            $table->text('delivery_address');
            
            // Cost & Payment
            $table->decimal('approx_cost', 10, 2)->nullable();
            $table->string('reference_number')->unique();
            
            // Status & Notes
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_production', 'ready', 'delivered', 'installed', 'completed'])
                ->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('street_id');
            $table->index('status');
            $table->index('reference_number');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('street_numbering_plates');
    }
};
