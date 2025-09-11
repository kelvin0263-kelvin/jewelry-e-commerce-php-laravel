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
        Schema::table('inventory_variations', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active')->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_variations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
