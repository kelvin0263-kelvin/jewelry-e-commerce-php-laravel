<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 产品名称
            $table->text('description'); // 产品描述
            $table->decimal('price', 8, 2); // 售价，总共8位数，小数点后2位
            $table->integer('quantity'); // 库存数量
            $table->string('image_path')->nullable(); // 图片路径，可以为空
            $table->boolean('is_visible')->default(false); // 是否可见
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
