<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Import HasFactory
use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Inventory; // ✅ Import Inventory

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'inventory_id',
        'image_path',
        'status',
    ];

    // Each product belongs to one inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    // Optional: fetch all variations linked to this product through inventory
    public function variations()
    {
        return $this->hasManyThrough(
            \App\Modules\Inventory\Models\InventoryVariation::class, // Final model
            Inventory::class,                                       // Intermediate model
            'id',           // Inventory local key
            'inventory_id', // Variation foreign key
            'inventory_id', // Product local key
            'id'            // Inventory local key
        );
    }

public static function forCustomers()
{
    return self::whereHas('inventory', fn($q) => $q->where('status', 'published'));
}
}
