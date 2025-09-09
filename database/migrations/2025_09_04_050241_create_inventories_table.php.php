<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['RingItem', 'NecklaceItem', 'EarringsItem', 'BraceletItem']);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->nullable();
            $table->integer('min_stock_level')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');

            // Type-specific attributes
            $table->string('stone_type')->nullable();       // RingItem
            $table->string('ring_size')->nullable();        // RingItem
            $table->integer('necklace_length')->nullable();// NecklaceItem
            $table->boolean('has_pendant')->nullable();    // NecklaceItem
            $table->string('earring_style')->nullable();   // EarringsItem
            $table->boolean('is_pair')->nullable();        // EarringsItem
            $table->string('bracelet_clasp')->nullable();  // BraceletItem
            $table->boolean('adjustable')->nullable();     // BraceletItem

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
