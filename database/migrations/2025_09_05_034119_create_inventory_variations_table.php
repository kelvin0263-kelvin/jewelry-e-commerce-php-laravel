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
        Schema::create('inventory_variations', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('sku')->unique();
            $table->foreignId('inventory_id') // Link to inventories table
                  ->constrained('inventories')
                  ->onDelete('cascade');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('material')->nullable();
            $table->decimal('price', 10, 2); // Variation price
            $table->integer('stock')->default(0); // Stock per variation
            $table->string('image_path')->nullable(); // Optional image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_variations');
    }
};
