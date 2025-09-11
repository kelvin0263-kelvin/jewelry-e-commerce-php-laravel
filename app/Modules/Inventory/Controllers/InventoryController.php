<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;
use App\Modules\Inventory\Factories\InventoryItemFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /** =========================================
     *  LIST INVENTORIES
     *  ========================================= */
    public function index()
    {
        $inventories = Inventory::with('variations') // ✅ eager load variations
            ->latest()
            ->paginate(15);

        foreach ($inventories as $inv) {
            $inv->total_quantity = $inv->variations->sum('stock'); // sum of all variations
        }

        return view('inventory::admin.inventory.index', compact('inventories'));
    }

    /** =========================================
     *  CREATE INVENTORY PAGE
     *  ========================================= */
    public function create()
    {
        return view('inventory::admin.inventory.create');
    }

    protected $priceRanges = [
        'RingItem' => [600, 1000],
        'NecklaceItem' => [400, 1100],
        'EarringsItem' => [400, 600],
        'BraceletItem' => [50, 100],
    ];


    /** =========================================
     *  STORE INVENTORY
     *  ========================================= */
public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'min_stock_level' => 'nullable|integer',
            'status' => 'nullable|in:draft,published',
            // Type-specific fields
            'stone_type' => 'nullable|string|max:50',
            'ring_size' => 'nullable|integer|min:4|max:12',
            'necklace_length' => 'nullable|integer|min:30|max:80',
            'has_pendant' => 'nullable|boolean',
            'earring_style' => 'nullable|string|max:50',
            'is_pair' => 'nullable|boolean',
            'bracelet_clasp' => 'nullable|string|max:50',
            'adjustable' => 'nullable|boolean',
            // Variations
            'variations' => 'nullable|array',
            'variations.*.sku' => 'nullable|string|max:50',
            'variations.*.color' => 'nullable|string|max:50',
            'variations.*.size' => 'nullable|string|max:50',
            'variations.*.material' => 'nullable|string|max:100',
            'variations.*.stock' => 'nullable|integer|min:0',
            'variations.*.image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Check duplicate SKUs inside request itself and globally (case-insensitive)
        if (!empty($data['variations'])) {
            // Normalize request SKUs
            $requestSkuNormalized = [];
            foreach ($data['variations'] as $v) {
                $sku = trim((string)($v['sku'] ?? ''));
                if ($sku !== '') {
                    $requestSkuNormalized[] = strtolower($sku);
                }
            }

            // Check duplicates within the same request
            if (count($requestSkuNormalized) !== count(array_unique($requestSkuNormalized))) {
                return back()->withInput()->with('error', 'Variation SKUs must be unique!');
            }

            // Check duplicates in database (GLOBAL, case-insensitive)
            $conflicts = [];
            foreach ($data['variations'] as $v) {
                $sku = trim((string)($v['sku'] ?? ''));
                if ($sku === '') { continue; }
                $exists = InventoryVariation::whereRaw('LOWER(sku) = ?', [strtolower($sku)])->exists();
                if ($exists) { $conflicts[] = strtoupper($sku); }
            }
            if (!empty($conflicts)) {
                $conflicts = array_values(array_unique($conflicts));
                return back()->withInput()->with('error', 'These SKUs already exist: ' . implode(', ', $conflicts));
            }
        }

        // ------------------------
        // Calculate inventory price from range
        // ------------------------
        $range = $this->priceRanges[$data['type']] ?? [0,0];
        $data['price'] = ($range[0] + $range[1]) / 2;

        // ✅ Create main inventory
        $inventory = Inventory::create($data);

        // ✅ Handle variations
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $imagePath = null;
                if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variation['image_path']->store('variations', 'public');
                }

                // ✅ Generate SKU if missing
                $sku = !empty($variation['sku']) ? strtoupper($variation['sku']) : null;
                if (!$sku) {
                    do {
                        $sku = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));
                    } while (
                        InventoryVariation::where('sku', $sku)->exists()
                    );
                }

                // Create inventory item using factory pattern
                $item = $inventory->createInventoryItem($variation);

                $inventory->variations()->create([
                    'sku' => $sku,
                    'color' => $variation['color'] ?? null,
                    'size' => $variation['size'] ?? null,
                    'material' => $variation['material'] ?? null,
                    'stock' => $variation['stock'] ?? 0,
                    'price' => $item->calculateValue(),
                    'image_path' => $imagePath,
                    'properties' => [
                        'description' => $item->getDescription(),
                        'calculated_value' => $item->calculateValue(),
                        'variation_data' => $variation,
                        'factory_created_at' => now()->toISOString(),
                    ],
                ]);
            }
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory created successfully');
    }


    /** =========================================
     *  EDIT INVENTORY
     *  ========================================= */
    public function edit($id)
    {
        $inventory = Inventory::with('variations', 'product')->findOrFail($id);

        if ($inventory->status === 'published') {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'You cannot edit a published inventory. Please set it to draft first.');
        }

        return view('inventory::admin.inventory.edit', compact('inventory'));
    }

    /** =========================================
     *  UPDATE INVENTORY
     *  ========================================= */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::with('product', 'variations')->findOrFail($id);

        if ($inventory->status === 'published') {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'You cannot update a published inventory. Please set it to draft first.');
        }

        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
                'description' => 'nullable|string',
                'quantity' => 'nullable|integer|min:0|max:999999',
                'status' => 'nullable|in:draft,published',

                // Type-specific fields
                'stone_type' => 'nullable|string|max:50',
                'ring_size' => 'nullable|integer|min:4|max:12',
                'necklace_length' => 'nullable|integer|min:30|max:80',
                'has_pendant' => 'nullable|boolean',
                'earring_style' => 'nullable|string|max:50',
                'is_pair' => 'nullable|boolean',
                'bracelet_clasp' => 'nullable|string|max:50',
                'adjustable' => 'nullable|boolean',

                // Variations
                'variations' => 'nullable|array',
                'delete_variations' => 'nullable|array',
                'variations.*.id' => 'nullable|integer|exists:inventory_variations,id',
                'variations.*.sku' => 'nullable|string|max:50',
                'variations.*.color' => 'nullable|string|max:50',
                'variations.*.size' => 'nullable|string|max:50',
                'variations.*.material' => 'nullable|string|max:100',
                'variations.*.stock' => 'nullable|integer|min:0|max:999999',
                'variations.*.image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'name.required' => 'The inventory name is required.',
                'type.required' => 'Please select a product type.',
                'price.numeric' => 'The price must be a valid number.',
                'quantity.integer' => 'Quantity must be a valid number.',
                'variations.*.sku.max' => 'SKU cannot be longer than 50 characters.',
                //'variations.*.stock.required' => 'Please enter stock quantity for each variation.',
                'variations.*.stock.integer' => 'Stock must be a valid number.',
                'variations.*.stock.min' => 'Stock cannot be negative.',
                'variations.*.stock.max' => 'Stock cannot exceed 999,999.',
                'variations.*.price.numeric' => 'Price must be a valid number.',
                'variations.*.price.max' => 'Price cannot exceed RM999,999.99.',
                'variations.*.image_path.image' => 'The uploaded file must be an image.',
                'variations.*.image_path.mimes' => 'Only JPG, JPEG, and PNG formats are allowed.',
                'variations.*.image_path.max' => 'Image size must not exceed 2MB.',
            ]
        );

        // ------------------------
        // Check duplicate SKUs (GLOBAL uniqueness)
        // ------------------------
        if (!empty($data['variations'])) {
            // Normalize and collect SKUs from request
            $requestSkus = [];
            foreach ($data['variations'] as $v) {
                $sku = strtoupper(trim($v['sku'] ?? ''));
                if ($sku !== '') {
                    $requestSkus[] = $sku;
                }
            }

            // Check duplicates within the same request
            if (count($requestSkus) !== count(array_unique($requestSkus))) {
                return back()->withInput()->with('error', 'Variation SKUs in the form must be unique!');
            }

            // Check duplicates in database (global), excluding the same variation row by ID if present
            $conflicts = [];
            foreach ($data['variations'] as $v) {
                $sku = strtoupper(trim($v['sku'] ?? ''));
                if ($sku === '') {
                    continue;
                }

                $query = InventoryVariation::where('sku', $sku);
                if (!empty($v['id'])) {
                    $query->where('id', '!=', (int) $v['id']);
                }
                if ($query->exists()) {
                    $conflicts[] = $sku;
                }
            }

            if (!empty($conflicts)) {
                $conflicts = array_values(array_unique($conflicts));
                return back()->withInput()->with('error', 'These SKUs already exist: ' . implode(', ', $conflicts));
            }
        }
        
        // ------------------------
        // Calculate price from range
        // ------------------------
        $range = $this->priceRanges[$data['type']] ?? [0,0];
        $data['price'] = ($range[0] + $range[1]) / 2;

        // ------------------------
        // Update inventory
        // ------------------------
        $inventory->update($data);

        // ------------------------
        // Update or create product (only sync when inventory is published)
        // ------------------------
        if ($inventory->status === 'published') {
            if ($inventory->product) {
                $inventory->product->update([
                    'name' => $data['name'],
                    'price' => $data['price'] ?? 0,
                    'description' => $data['description'] ?? '',
                    'status' => $data['status'] ?? 'draft',
                ]);
            } else {
                $inventory->product()->create([
                    'name' => $data['name'],
                    'price' => $data['price'] ?? 0,
                    'description' => $data['description'] ?? '',
                    'status' => $data['status'] ?? 'draft',
                ]);
            }
        } else {
            // For draft inventories, do NOT sync data to product module
            // Data will only be synced when inventory is published
            // Only create product if it doesn't exist (for new inventories)
            if (!$inventory->product) {
                $inventory->product()->create([
                    'name' => $data['name'],
                    'price' => $data['price'] ?? 0,
                    'description' => $data['description'] ?? '',
                    'status' => 'draft',
                ]);
            }
            // If product already exists, do NOT update it during draft updates
        }

        // ------------------------
        // Delete removed variations
        // ------------------------
        if ($request->has('delete_variations') && !empty($request->delete_variations)) {
            $variationsToDelete = InventoryVariation::whereIn('id', $request->delete_variations)->get();
            foreach ($variationsToDelete as $variation) {
                if ($variation->image_path) {
                    Storage::disk('public')->delete($variation->image_path);
                }
            }
            InventoryVariation::whereIn('id', $request->delete_variations)->delete();
        }

        // ------------------------
        // Update or create variations
        // ------------------------
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $imagePath = null;
                if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variation['image_path']->store('variations', 'public');
                }

                $sku = !empty($variation['sku']) ? strtoupper($variation['sku']) : null;
                if (!$sku) {
                    do {
                        $sku = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));
                    } while (
                        InventoryVariation::where('sku', $sku)->exists()
                    );
                }

                $item = $inventory->createInventoryItem($variation);

                $variationData = [
                    'sku' => $sku,
                    'color' => $variation['color'] ?? null,
                    'size' => $variation['size'] ?? null,
                    'material' => $variation['material'] ?? null,
                    'stock' => $variation['stock'] ?? 0,
                    'price' => $item->calculateValue(),
                    'image_path' => $imagePath ?? ($variation['old_image'] ?? null),
                    'properties' => [
                        'description' => $item->getDescription(),
                        'calculated_value' => $item->calculateValue(),
                        'variation_data' => $variation,
                        'factory_updated_at' => now()->toISOString(),
                    ],
                ];

                if (!empty($variation['id'])) {
                    $invVar = $inventory->variations()->find($variation['id']);
                    if ($invVar) {
                        if ($imagePath && $invVar->image_path) {
                            Storage::disk('public')->delete($invVar->image_path);
                        }
                        $invVar->update($variationData);
                    }
                } else {
                    $inventory->variations()->create($variationData);
                }
            }
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory, Product, and Variations updated successfully');
    }


    /** =========================================
     *  DELETE INVENTORY
     *  ========================================= */
    public function destroy($id)
    {
        $inventory = Inventory::with('variations.product', 'product')->findOrFail($id);

        if ($inventory->status === 'published') {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'You cannot delete a published inventory. Please set it to draft first.');
        }

        // Delete all products associated with inventory variations first
        foreach ($inventory->variations as $variation) {
            if ($variation->product) {
                // Delete associated images for variation products
                if ($variation->product->customer_images) {
                    foreach ($variation->product->customer_images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }
                if ($variation->product->image_path) {
                    Storage::disk('public')->delete($variation->product->image_path);
                }
                $variation->product->delete();
            }
            
            // Delete variation images
            if ($variation->image_path) {
                Storage::disk('public')->delete($variation->image_path);
            }
        }

        // Delete the main product associated with the inventory
        if ($inventory->product) {
            // Delete associated images for main product
            if ($inventory->product->customer_images) {
                foreach ($inventory->product->customer_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            if ($inventory->product->image_path) {
                Storage::disk('public')->delete($inventory->product->image_path);
            }
            $inventory->product->delete();
        }

        // Delete all variations
        $inventory->variations()->delete();
        
        // Finally delete the inventory
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory and all associated products deleted successfully');
    }

    /** =========================================
     *  TOGGLE STATUS
     *  ========================================= */
    public function toggleStatus($id)
    {
        $inventory = Inventory::with(['product', 'variations.product'])->findOrFail($id);

        $inventory->status = $inventory->status === 'published' ? 'draft' : 'published';
        $inventory->save();

        if ($inventory->status === 'draft') {
            // When unpublishing inventory, mark all related products as issued
            $unpublishedProducts = [];
            foreach ($inventory->variations as $variation) {
                if ($variation->product) {
                    $variation->product->update([
                        'is_visible' => false,
                        'status' => 'issued',
                        'issued_at' => now(),
                        'issued_by' => auth()->id(),
                    ]);
                    $unpublishedProducts[] = $variation->product->name;
                }
            }
            
            // Set flag to show inventory unpublish notification with product details
            if (!empty($unpublishedProducts)) {
                session(['inventory_unpublished' => [
                    'inventory_name' => $inventory->name,
                    'products' => $unpublishedProducts,
                    'message' => 'Products have been delisted: ' . implode(', ', $unpublishedProducts)
                ]]);
            }
        } else {
            // When publishing inventory, reset issued products back to normal
            $hasIssuedProducts = false;
            $republishedProducts = [];
            foreach ($inventory->variations as $variation) {
                if ($variation->product && $variation->product->status === 'issued') {
                    $hasIssuedProducts = true;
                    $republishedProducts[] = $variation->product->name;
                    // Reset issued products back to pending status
                    $variation->product->update([
                        'status' => 'pending',
                        'is_visible' => true, // Make visible again when republished
                        'issued_at' => null,
                        'issued_by' => null,
                        // Clear user-facing information to force recreation
                        'marketing_description' => null,
                        'customer_images' => null,
                        'product_video' => null,
                        'discount_price' => null,
                    ]);
                }
            }
            
            // Update the main product status and sync data
            if ($inventory->product) {
                // Check if product was previously draft (new product)
                if ($inventory->product->status === 'draft') {
                    $inventory->product->update([
                        'name' => $inventory->name,
                        'price' => $inventory->price,
                        'description' => $inventory->description,
                        'status' => $inventory->status,
                        'published_at' => now(), // Set published_at when inventory is published
                        'published_by' => auth()->id(),
                    ]);
                    // Set flag to show new product added notification with product details
                    session(['new_product_added' => [
                        'sku' => $inventory->variations->first()?->sku ?? $inventory->id,
                        'name' => $inventory->name,
                        'changes' => 'New product added to inventory'
                    ]]);
                } else {
                    $inventory->product->update([
                        'name' => $inventory->name,
                        'price' => $inventory->price,
                        'description' => $inventory->description,
                        'status' => $inventory->status,
                        'published_at' => now(), // Update published_at when inventory is republished
                        'published_by' => auth()->id(),
                    ]);
                    // Set flag to show inventory changes notification
                    if ($hasIssuedProducts && !empty($republishedProducts)) {
                        session(['inventory_republished' => [
                            'inventory_name' => $inventory->name,
                            'products' => $republishedProducts,
                            'message' => 'Products have been relisted: ' . implode(', ', $republishedProducts)
                        ]]);
                    } else {
                        // For regular republish without issued products
                        session(['inventory_republished' => [
                            'inventory_name' => $inventory->name,
                            'message' => 'Inventory has been republished'
                        ]]);
                    }
                }
            } else {
                $newProduct = Product::create([
                    'inventory_id' => $inventory->id,
                    'name' => $inventory->name,
                    'price' => 0,
                    'status' => 'draft', // New products start as draft, not published
                ]);
                // Set flag to show new product added notification with product details
                session(['new_product_added' => [
                    'sku' => $inventory->variations->first()?->sku ?? $inventory->id,
                    'name' => $inventory->name,
                    'changes' => 'New product added to inventory'
                ]]);
            }
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "Inventory status updated to {$inventory->status}");
    }

public function dashboard()
{
    $inventories = Inventory::with('variations')->get(); // fetch all inventories

    $totalProducts = $inventories->count(); // total inventory items
    $totalStock = $inventories->sum(function ($inv) {
        return $inv->variations->sum('stock'); // sum of all variation stock
    });
    $lowStockCount = $inventories->filter(function ($inv) {
        return $inv->variations->sum('stock') < $inv->min_stock_level;
    })->count();
    $totalVariations = $inventories->sum(function ($inv) {
        return $inv->variations->count(); // total number of variations
    });

    return view('inventory::admin.inventory.dashboard', compact(
        'inventories',     // pass inventories if your Blade needs it
        'totalProducts',
        'totalStock',
        'lowStockCount',
        'totalVariations'
    ));
}

}
