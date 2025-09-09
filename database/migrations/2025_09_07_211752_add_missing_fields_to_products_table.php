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
            $table->string('sku')->nullable()->after('name');
            $table->text('marketing_description')->nullable()->after('description');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->json('customer_images')->nullable()->after('image_path');
            $table->string('category')->nullable()->after('status');
            $table->json('features')->nullable()->after('category');
            $table->foreignId('published_by')->nullable()->after('features')->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable()->after('published_by');
            $table->boolean('is_visible')->default(false)->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'marketing_description',
                'discount_price',
                'customer_images',
                'category',
                'features',
                'published_by',
                'published_at',
                'is_visible'
            ]);
        });
    }
};
