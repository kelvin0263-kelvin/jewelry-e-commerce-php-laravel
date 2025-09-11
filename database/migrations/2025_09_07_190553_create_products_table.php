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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Product name
            $table->text('description')->nullable(); // Optional description
            $table->decimal('price', 10, 2)->nullable(); // Selling price (optional)
            $table->foreignId('inventory_id') // Link to inventories table
                  ->constrained('inventories')
                  ->onDelete('cascade');
            $table->string('image_path')->nullable(); // Main image
            $table->enum('status', ['draft', 'pending_review', 'approved', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};