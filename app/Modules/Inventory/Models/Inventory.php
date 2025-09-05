<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'name',
        'type',
        'quantity',
        'min_stock_level',
        'status',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'inventory_id');
    }

    public function variations()
    {
        return $this->hasMany(InventoryVariation::class, 'inventory_id');
    }

    public function getTotalStockAttribute()
    {
        return $this->variations->sum('stock');
    }

    public function isLowStock()
    {
        return $this->total_stock <= $this->min_stock_level;
    }

    protected static function booted()
    {
        // On create
        static::created(function ($inventory) {
            if ($inventory->status === 'published') {
                $inventory->createProductIfNotExists();
            }
        });

        // On update
        static::updated(function ($inventory) {
            if ($inventory->wasChanged('status') && $inventory->status === 'published') {
                $inventory->createProductIfNotExists();
            }
        });
    }

    // Create product if it doesn’t exist
   public function createProductIfNotExists()
{
    if (!$this->product()->exists()) { // database check
        Product::create([
            'inventory_id' => $this->id,
            'name' => $this->name,
            'price' => 0,
            'status' => 'published',
        ]);
    }
}
}
