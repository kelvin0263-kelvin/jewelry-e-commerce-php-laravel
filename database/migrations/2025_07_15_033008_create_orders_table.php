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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Money-related fields
            $table->decimal('subtotal', 10, 2);         // before discount & shipping
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);     // final amount user paid

            // Promo & Payment
            $table->string('promo_code')->nullable();
            $table->string('payment_method');           
            $table->enum('payment_status', ['completed'])->default('completed'); 

            // Shipping
            $table->string('shipping_address');
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_method');          
            $table->string('tracking_number')->nullable();

            // Order lifecycle
            $table->enum('status', ['pending', 'shipped', 'delivered', 'completed', 'refund'])
                ->default('pending');

            // Refund status
            $table->enum('refund_status', ['refunding', 'rejected', 'refunded'])
                ->nullable();

            $table->string('refund_reason')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};


?>