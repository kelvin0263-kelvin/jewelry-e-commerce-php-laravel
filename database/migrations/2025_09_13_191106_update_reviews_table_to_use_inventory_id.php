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
        Schema::table('reviews', function (Blueprint $table) {
            // First, add the new inventory_id column
            $table->foreignId('inventory_id')->nullable()->after('id')->constrained('inventories')->onDelete('cascade');
            
            // Add index for better performance
            $table->index('inventory_id');
        });

        // Migrate existing data: update inventory_id based on product_id
        DB::statement('
            UPDATE reviews 
            SET inventory_id = (
                SELECT p.inventory_id 
                FROM products p 
                WHERE p.id = reviews.product_id
            )
        ');

        // Make inventory_id not nullable after data migration
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable(false)->change();
        });

        // Drop the old product_id column and its foreign key
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Add back product_id column
            $table->foreignId('product_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Migrate data back: update product_id based on inventory_id
        // Note: This will only work if there's a one-to-one relationship
        // For multiple products per inventory, we'll take the first one
        DB::statement('
            UPDATE reviews 
            SET product_id = (
                SELECT p.id 
                FROM products p 
                WHERE p.inventory_id = reviews.inventory_id
                LIMIT 1
            )
        ');

        // Make product_id not nullable
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
        });

        // Drop inventory_id column and its foreign key
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropIndex(['inventory_id']);
            $table->dropColumn('inventory_id');
        });
    }
};