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
            $table->unsignedBigInteger('inventory_variation_id')->nullable()->after('inventory_id');
            $table->foreign('inventory_variation_id')->references('id')->on('inventory_variations')->onDelete('cascade');
            $table->index('inventory_variation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['inventory_variation_id']);
            $table->dropIndex(['inventory_variation_id']);
            $table->dropColumn('inventory_variation_id');
        });
    }
};
