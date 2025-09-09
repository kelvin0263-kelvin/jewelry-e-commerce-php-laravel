<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(\App\Modules\Product\Models\Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
