<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'reviewer_name',
        'rating',
        'title',
        'content',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->hasOneThrough(
            \App\Modules\Inventory\Models\Inventory::class,
            Product::class,
            'id', // Foreign key on products table
            'id', // Foreign key on inventories table
            'product_id', // Local key on reviews table
            'inventory_id' // Local key on products table
        );
    }
}
