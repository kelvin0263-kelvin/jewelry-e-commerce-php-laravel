<?php

namespace App\Modules\Cart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    // A cart item belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A cart item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
