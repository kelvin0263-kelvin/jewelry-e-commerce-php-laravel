<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'inventory_id',
        'reviewer_name',
        'rating',
        'title',
        'content',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function inventory()
    {
        return $this->belongsTo(\App\Modules\Inventory\Models\Inventory::class, 'inventory_id');
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            \App\Modules\Inventory\Models\Inventory::class,
            'id', // Foreign key on inventories table
            'inventory_id', // Foreign key on products table
            'inventory_id', // Local key on reviews table
            'id' // Local key on inventories table
        );
    }
}
