<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Import HasFactory
use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Inventory; // ✅ Import Inventory
use App\Modules\User\Models\User; // ✅ Import User

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'marketing_description',
        'price',
        'selling_price',
        'discount_price',
        'sku',
        'product_id',
        'inventory_id',
        'inventory_variation_id',
        'image_path',
        'customer_images',
        'product_video',
        'status',
        'category',
        'features',
        'published_by',
        'published_at',
        'is_visible',
        'issued_at',
        'issued_by',
    ];

    protected $casts = [
        'customer_images' => 'array',
        'features' => 'array',
        'published_at' => 'datetime',
        'issued_at' => 'datetime',
        'price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

// 关联发布者 (Admin user)
public function publisher()
{
    return $this->belongsTo(User::class, 'published_by');
}

// 关联下架者 (Admin user)
public function issuer()
{
    return $this->belongsTo(User::class, 'issued_by');
}

    // Each product belongs to one inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    // Each product belongs to one inventory variation (SKU)
    public function variation()
    {
        return $this->belongsTo(\App\Modules\Inventory\Models\InventoryVariation::class, 'inventory_variation_id');
    }

    // Each product has many reviews
    public function reviews()
    {
        return $this->hasMany(\App\Modules\Product\Models\Review::class, 'product_id');
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

/** =========================
 * Booted model events
 * ========================= */
protected static function booted()
{
    static::deleting(function ($product) {
        // Delete associated reviews
        \App\Modules\Product\Models\Review::where('product_id', $product->id)->delete();
        
        // Delete from wishlists (if wishlist functionality exists)
        if (class_exists('\App\Modules\Product\Models\Wishlist')) {
            \App\Modules\Product\Models\Wishlist::where('product_id', $product->id)->delete();
        }
        
        // Delete from cart items (if cart functionality exists)
        if (class_exists('\App\Modules\Product\Models\Cart')) {
            \App\Modules\Product\Models\Cart::where('product_id', $product->id)->delete();
        }
        
        // Delete associated images
        if ($product->customer_images) {
            foreach ($product->customer_images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
            }
        }
        if ($product->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image_path);
        }
    });
}

/**
 * Listen for inventory status changes and delete products when inventory is unpublished
 */
public static function boot()
{
    parent::boot();
    
    // Listen for inventory status changes
    \App\Modules\Inventory\Models\Inventory::updated(function ($inventory) {
        // Check if inventory was changed from published to draft (unpublished)
        if ($inventory->wasChanged('status') && $inventory->status === 'draft') {
            // Mark all products as issued (rejected) instead of deleting them
            $productsToUpdate = self::where('inventory_id', $inventory->id)->get();
            
            foreach ($productsToUpdate as $product) {
                $product->update([
                    'status' => 'issued',
                    'issued_at' => now(),
                    'is_visible' => false
                ]);
            }
            
            // Also update products associated with inventory variations
            $variationProducts = self::whereHas('variation', function($query) use ($inventory) {
                $query->where('inventory_id', $inventory->id);
            })->get();
            
            foreach ($variationProducts as $product) {
                $product->update([
                    'status' => 'issued',
                    'issued_at' => now(),
                    'is_visible' => false
                ]);
            }
            
            // Set session flag for notification
            session(['inventory_unpublished' => [
                'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                'name' => $inventory->name,
                'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                'changes' => 'Products have been delisted and marked as issued'
            ]]);
        }
        
        // Check if inventory was changed from draft to published (republished)
        if ($inventory->wasChanged('status') && $inventory->status === 'published') {
            // Set session flag for republish notification
            session(['inventory_republished' => [
                'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                'name' => $inventory->name,
                'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                'changes' => 'Inventory has been republished'
            ]]);
        }
    });
}
}
