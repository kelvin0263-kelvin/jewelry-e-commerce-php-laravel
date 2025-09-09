<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Product\Models\Product;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'quantity',
        'min_stock_level',
        'status',
        // Type-specific fields
        'stone_type',
        'ring_size',
        'necklace_length',
        'has_pendant',
        'earring_style',
        'is_pair',
        'bracelet_clasp',
        'adjustable',
    ];

    protected $casts = [
        'has_pendant' => 'boolean',
        'is_pair' => 'boolean',
        'adjustable' => 'boolean',
        'price' => 'decimal:2',
    ];

    /** =========================
     * Relationships
     * ========================= */
    public function product()
    {
        return $this->hasOne(Product::class, 'inventory_id');
    }

    public function variations()
    {
        return $this->hasMany(InventoryVariation::class, 'inventory_id');
    }

    /** =========================
     * Attributes
     * ========================= */
    public function getTotalStockAttribute()
    {
        return $this->variations->sum('stock');
    }

    public function isLowStock()
    {
        return $this->total_stock <= $this->min_stock_level;
    }

    /** =========================
     * Booted model events
     * ========================= */
    protected static function booted()
    {
        static::created(function ($inventory) {
            if ($inventory->status === 'published') {
                $inventory->createProductIfNotExists();
            }
        });

        static::updated(function ($inventory) {
            if ($inventory->wasChanged('status') && $inventory->status === 'published') {
                $inventory->createProductIfNotExists();
            }
        });
    }

    /** =========================
     * Helpers
     * ========================= */
    public function createProductIfNotExists()
    {
        if (!$this->product()->exists()) {
            Product::create([
                'inventory_id' => $this->id,
                'name' => $this->name,
                'price' => $this->price ?? 0,
                'description' => $this->description ?? '',
                'status' => $this->status ?? 'draft',
            ]);
        }
    }

    /** =========================
     * Factory helper
     * ========================= */
    public function createInventoryItem(array $variation = [])
    {
        return \App\Modules\Inventory\Factories\InventoryItemFactory::create(
            $this->type,
            $variation['material'] ?? 'Unknown',
            $variation['price'] ?? $this->price ?? 0,
            $this->toArray() // Pass all inventory data including type-specific fields
        );
    }

    /**
     * Get type-specific options for this inventory type
     */
    public function getTypeOptions()
    {
        return \App\Modules\Inventory\Factories\InventoryItemFactory::getTypeOptions($this->type);
    }

    /**
     * Validate type-specific data for this inventory
     */
    public function validateTypeData()
    {
        return \App\Modules\Inventory\Factories\InventoryItemFactory::validateTypeData($this->type, $this->toArray());
    }

    /**
     * Get calculated value using factory pattern
     */
    public function getCalculatedValue()
    {
        $item = $this->createInventoryItem();
        return $item->calculateValue();
    }

    /**
     * Get description using factory pattern
     */
    public function getItemDescription()
    {
        $item = $this->createInventoryItem();
        return $item->getDescription();
    }

     public static array $typePriceRange = [
        'RingItem' => '600 - 1000',
        'NecklaceItem' => '400 - 1100',
        'EarringsItem' => '400 - 600',
        'BraceletItem' => '50 - 100',
    ];

    public function getPriceRangeAttribute(): string
    {
        return self::$typePriceRange[$this->type] ?? '0';
    }
}
