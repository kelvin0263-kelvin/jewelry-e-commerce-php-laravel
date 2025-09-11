<?php

namespace App\Modules\Order\Models;
use App\Modules\Product\Models\Product;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'order_id'
    ];


    // ✅ Each OrderItem belongs to one Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ✅ Each OrderItem belongs to one Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}


?>