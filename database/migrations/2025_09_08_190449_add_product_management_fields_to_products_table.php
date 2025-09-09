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
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'marketing_description')) {
                $table->text('marketing_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'customer_images')) {
                $table->json('customer_images')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable()->after('status');
            }
            if (!Schema::hasColumn('products', 'features')) {
                $table->json('features')->nullable()->after('category');
            }
            if (!Schema::hasColumn('products', 'published_by')) {
                $table->foreignId('published_by')->nullable()->after('features')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('products', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('published_by');
            }
            if (!Schema::hasColumn('products', 'is_visible')) {
                $table->boolean('is_visible')->default(false)->after('published_at');
            }
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
