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
        // Check if inventory was changed from draft to published (new product added)
        if ($inventory->wasChanged('status') && $inventory->status === 'published') {
            // Check if this is a new inventory (no existing products)
            $existingProducts = self::where('inventory_id', $inventory->id)->count();
            
            if ($existingProducts === 0) {
                // This is a new product from inventory module
                session(['new_product_added' => [
                    'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                    'name' => $inventory->name,
                    'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                    'changes' => 'New product added from inventory module'
                ]]);
            } else {
                // This is a republished inventory - check if there are rejected products
                $rejectedProducts = self::where('inventory_id', $inventory->id)
                    ->where('status', 'rejected')
                    ->get();
                
                if ($rejectedProducts->count() > 0) {
                    // Update rejected products to pending status
                    foreach ($rejectedProducts as $product) {
                        $product->update([
                            'status' => 'pending',
                            'issued_at' => null,
                            'issued_by' => null
                        ]);
                    }
                    
                    session(['inventory_republished' => [
                        'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                        'name' => $inventory->name,
                        'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                        'changes' => 'Inventory has been republished - products status changed to pending'
                    ]]);
                } else {
                    // Check if this is truly a republish (not just a new product with existing records)
                    $hasPublishedProducts = self::where('inventory_id', $inventory->id)
                        ->whereNotNull('published_at')
                        ->exists();
                    
                    if ($hasPublishedProducts) {
                        // Only show republish notification if there were previously published products
                        session(['inventory_republished' => [
                            'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                            'name' => $inventory->name,
                            'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                            'changes' => 'Inventory has been republished'
                        ]]);
                    }
                }
            }
        }
        
        // Check for inventory changes (quantity, price, etc.) that need sync
        if ($inventory->wasChanged(['total_stock', 'price', 'name', 'description']) && $inventory->status === 'published') {
            // Check if there are any products for this inventory
            $existingProducts = self::where('inventory_id', $inventory->id)->count();
            
            if ($existingProducts > 0) {
                session(['inventory_changes' => [
                    'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                    'name' => $inventory->name,
                    'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                    'changes' => 'Inventory data has been updated - sync required'
                ]]);
            }
        }
        
        // Check if inventory was changed from published to draft (unpublished)
        if ($inventory->wasChanged('status') && $inventory->status === 'draft') {
            // Mark all products as issued (rejected) instead of deleting them
            $productsToUpdate = self::where('inventory_id', $inventory->id)->get();
            
            foreach ($productsToUpdate as $product) {
                $product->update([
                    'status' => 'rejected',
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
                    'status' => 'rejected',
                    'issued_at' => now(),
                    'is_visible' => false
                ]);
            }
            
            // Set session flag for notification
            session(['inventory_unpublished' => [
                'sku' => $inventory->variations->first()?->sku ?? 'N/A',
                'name' => $inventory->name,
                'updated_at' => $inventory->updated_at->format('M d, Y H:i'),
                'changes' => 'Products have been delisted and marked as rejected'
            ]]);
        }
    });
}
}
