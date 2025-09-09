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
        Schema::table('products', function (Blueprint $table) {
            // Modify price fields to support larger values
            $table->decimal('price', 15, 2)->nullable()->change();
            $table->decimal('discount_price', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert price fields to original size
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->decimal('discount_price', 10, 2)->nullable()->change();
        });
    }
};
