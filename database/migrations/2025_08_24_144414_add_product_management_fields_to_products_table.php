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
            // Inventory-only fields (basic/internal data)
            $table->string('internal_image_path')->nullable()->after('image_path'); // Basic image for inventory
            $table->enum('status', ['draft', 'pending_review', 'approved', 'published'])->default('draft')->after('is_visible');
            
            // Product Management fields (enhanced/marketing data) 
            $table->text('marketing_description')->nullable()->after('description'); // Enhanced description for customers
            $table->json('customer_images')->nullable()->after('internal_image_path'); // Multiple customer-facing images
            $table->string('category')->nullable()->after('marketing_description'); // Product category
            $table->json('features')->nullable()->after('category'); // Product features/specifications
            $table->decimal('discount_price', 10, 2)->nullable()->after('price'); // Sale price
            $table->string('sku')->nullable()->unique()->after('name'); // Stock keeping unit
            $table->integer('min_stock_level')->default(5)->after('quantity'); // Minimum stock alert
            
            // Publishing control
            $table->timestamp('published_at')->nullable()->after('status'); // When published to customers
            $table->unsignedBigInteger('published_by')->nullable()->after('published_at'); // Who published it
            
            // Add foreign key for published_by
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['published_by']);
            $table->dropColumn([
                'internal_image_path',
                'status', 
                'marketing_description',
                'customer_images',
                'category',
                'features',
                'discount_price',
                'sku',
                'min_stock_level',
                'published_at',
                'published_by'
            ]);
        });
    }
};