<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;
use App\Modules\Inventory\Factories\InventoryItemFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\log;


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

        Log::info('Inventory created', [
            'inventory_id' => $inventory->id,
            'name' => $inventory->name,
            'user_id' => auth()->id(),
            'time' => now()->toDateTimeString(),
        ]);


        // ✅ Handle variations
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $imagePath = null;

                if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $variation['image_path'];

                    // ✅ Secure: Check MIME type
                    $allowedMimes = ['image/jpeg', 'image/png'];
                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        return back()->withInput()->with('error', 'Uploaded file must be a valid image (JPG, PNG).');
                    }

                    // ✅ Secure: Sanitize filename
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());

                    // Move the file
                    $file->move(public_path('images'), $filename);

                    // ✅ Save only relative path
                    $imagePath = 'images/' . $filename;
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

        Log::info('Inventory updated', [
            'inventory_id' => $inventory->id,
            'name' => $inventory->name,
            'user_id' => auth()->id(),
            'time' => now()->toDateTimeString(),
        ]);


        // ------------------------
        // Update or create product
        // ------------------------
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

        // ------------------------
        // Delete removed variations
        // ------------------------
        if ($request->has('variations')) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['id']) && !empty($variationData['delete'])) {
                    $variation = InventoryVariation::find($variationData['id']);
                    if ($variation) {
                        // Delete image
                        if ($variation->image_path) {
                            Storage::disk('public')->delete($variation->image_path);
                        }
                        // Delete from DB
                        $variation->delete();
                    }
                }
            }
        }

        // ------------------------
        // Update or create variations
        // ------------------------
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                // If variation already exists, get it, else create a new one
                $variationModel = isset($variation['id'])
                    ? InventoryVariation::find($variation['id'])
                    : new InventoryVariation(['inventory_id' => $inventory->id]);

                 // ✅ Ensure SKU
                if (!empty($variation['sku'])) {
                    $sku = strtoupper(trim($variation['sku']));
                } elseif (!empty($variation['id']) && $variationModel) {
                    $sku = $variationModel->sku; // reuse existing SKU
                } else {
                    do {
                        $sku = 'INV-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));
                    } while (InventoryVariation::where('sku', $sku)->exists());
                }

                $imagePath = $variationModel->image_path ?? null;

                if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $variation['image_path'];

                    // ✅ Delete old image if it exists
                    if (!empty($variationModel->image_path) && file_exists(public_path($variationModel->image_path))) {
                        unlink(public_path($variationModel->image_path));
                    }

                    // ✅ Secure: Check MIME type
                    $allowedMimes = ['image/jpeg', 'image/png'];
                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        return back()->withInput()->with('error', 'Uploaded file must be a valid image (JPG, PNG).');
                    }

                    // ✅ Secure: Sanitize filename
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());

                    // Move the file
                    $file->move(public_path('images'), $filename);

                    // Save new image path
                    $imagePath = 'images/' . $filename;
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
        

        $inventory = Inventory::with('variations', 'product')->findOrFail($id);

        Log::warning('Inventory deleted', [
            'inventory_id' => $inventory->id,
            'name' => $inventory->name,
            'user_id' => auth()->id(),
            'time' => now()->toDateTimeString(),
        ]);

        if ($inventory->status === 'published') {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'You cannot delete a published inventory. Please set it to draft first.');
        }

        foreach ($inventory->variations as $variation) {
            if ($variation->image_path) {
                Storage::disk('public')->delete($variation->image_path);
            }
        }

        $inventory->variations()->delete();
        if ($inventory->product) {
            $inventory->product->delete();
        }
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory deleted successfully');
    }

    /** =========================================
     *  TOGGLE STATUS
     *  ========================================= */
    public function toggleStatus($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);

        $inventory->status = $inventory->status === 'published' ? 'draft' : 'published';
        $inventory->save();

        if ($inventory->product) {
            $inventory->product->update(['status' => $inventory->status]);
        } else {
            Product::create([
                'inventory_id' => $inventory->id,
                'name' => $inventory->name,
                'price' => 0,
                'status' => $inventory->status,
            ]);
        }

        Log::info('Inventory status changed', [
            'inventory_id' => $inventory->id,
            'name' => $inventory->name,
            'new_status' => $inventory->status,
            'user_id' => auth()->id(),
            'time' => now()->toDateTimeString(),
        ]);


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

// InventoryController.php

public function list()
{
    // Get only edited inventories
    $inventories = Inventory::with('variations')
        ->whereColumn('updated_at', '!=', 'created_at') // only edited
        ->orderBy('updated_at', 'desc')
        ->get();

    // Pass data to Blade view
    return view('inventory::admin.inventory.list', compact('inventories'));
}



}
