<?php

namespace App\Modules\Order\Models;

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


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}


?>