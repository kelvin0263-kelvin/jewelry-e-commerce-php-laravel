<?php
// Author: Ng Yanding
// Date: 2025-09-15
namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryVariation extends Model
{
    use HasFactory;

    protected $table = 'inventory_variations';

    protected $fillable = [
        'sku',
        'inventory_id',
        'color',
        'size',
        'material',
        'price',
        'stock',
        'image_path',
        'properties',
        'status',
    ];

    protected $casts = [
        'properties' => 'array',
        'price' => 'decimal:2',
    ];

    // Each variation belongs to one inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    // Each variation can have one product
    public function product()
    {
        return $this->hasOne(\App\Modules\Product\Models\Product::class, 'inventory_variation_id');
    }

    //Booted model events
    protected static function booted()
    {
        static::created(function ($variation) {
            // Update inventory total stock when variation is created
            $variation->inventory->updateTotalStock();
        });

        static::updated(function ($variation) {
            // Update inventory total stock when variation is updated
            $variation->inventory->updateTotalStock();
        });

        static::deleting(function ($variation) {
            // Delete the product associated with this variation
            if ($variation->product) {
                $variation->product->delete();
            }
        });

        static::deleted(function ($variation) {
            // Update inventory total stock when variation is deleted
            $variation->inventory->updateTotalStock();
        });
    }
}
