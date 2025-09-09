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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // For guest users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // For logged in users
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination of session/user and product
            $table->unique(['session_id', 'product_id']);
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
