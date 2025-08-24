<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_amount', 'status'];

    public function user()
    {
        return $this->belongsTo(\App\Modules\User\Models\User::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Modules\Product\Models\Product::class)->withPivot('quantity', 'price');
    }
}
