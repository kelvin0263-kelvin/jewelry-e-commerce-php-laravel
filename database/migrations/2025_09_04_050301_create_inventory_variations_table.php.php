<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_variations', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('material')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->json('properties')->nullable(); // extra data for variations
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_variations');
    }
};
