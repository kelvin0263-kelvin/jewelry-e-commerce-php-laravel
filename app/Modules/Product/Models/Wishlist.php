<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

namespace App\Modules\Product\Models;

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
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
