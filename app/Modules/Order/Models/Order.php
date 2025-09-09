<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'subtotal', 'discount', 'shipping_cost', 'total_amount',
        'promo_code', 'payment_method', 'payment_status',
        'shipping_address', 'shipping_postal_code',
        'shipping_method', 'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

?>