<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /** =========================================
     *  LIST INVENTORIES
     *  ========================================= */
    public function index()
    {
        $inventories = Inventory::latest()->paginate(15);
        return view('inventory::admin.inventory.index', compact('inventories'));
    }

    /** =========================================
     *  CREATE INVENTORY PAGE
     *  ========================================= */
    public function create()
    {
        return view('inventory::admin.inventory.create');
    }

    /** =========================================
     *  STORE INVENTORY
     *  ========================================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
            'quantity' => 'nullable|integer',
            'min_stock_level' => 'nullable|integer',
            'status' => 'nullable|in:draft,published',
            'variations' => 'nullable|array',
            'variations.*.sku' => 'nullable|string|max:50',
            'variations.*.color' => 'nullable|string|max:50',
            'variations.*.size' => 'nullable|string|max:50',
            'variations.*.material' => 'nullable|string|max:100',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.stock' => 'nullable|integer|min:0',
            'variations.*.image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $inventory = Inventory::create($data);

        // ✅ Create variations with image upload
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $imagePath = null;
                if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variation['image_path']->store('variations', 'public');
                }

                $inventory->variations()->create([
                    'sku' => !empty($variation['sku'])
                        ? strtoupper($variation['sku'])
                        : 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    'color' => $variation['color'] ?? null,
                    'size' => $variation['size'] ?? null,
                    'material' => $variation['material'] ?? null,
                    'stock' => $variation['stock'] ?? 0,
                    'price' => $variation['price'] ?? 0,
                    'image_path' => $imagePath,
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

    // ✅ Prevent updates if published
    if ($inventory->status === 'published') {
        return redirect()->route('admin.inventory.index')
            ->with('error', 'You cannot update a published inventory. Please set it to draft first.');
    }

    $data = $request->validate([
        'name' => 'required|string',
        'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
        'quantity' => 'nullable|integer',
        'status' => 'nullable|in:draft,published',
        'price' => 'nullable|numeric',
        'description' => 'nullable|string',
        'variations' => 'nullable|array',
        'delete_variations' => 'nullable|array',
        'variations.*.id' => 'nullable|integer|exists:inventory_variations,id',
        'variations.*.sku' => 'nullable|string|max:50',
        'variations.*.color' => 'nullable|string|max:50',
        'variations.*.size' => 'nullable|string|max:50',
        'variations.*.material' => 'nullable|string|max:100',
        'variations.*.price' => 'nullable|numeric|min:0',
        'variations.*.stock' => 'nullable|integer|min:0',
        'variations.*.image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // ✅ Check for duplicate SKUs inside the same request
    if (!empty($data['variations'])) {
        $skus = array_filter(array_map(fn($v) => strtoupper($v['sku'] ?? ''), $data['variations']));
        if (count($skus) !== count(array_unique($skus))) {
            return back()->withInput()->with('error', 'Variation SKUs must be unique!');
        }

        // ✅ Check against DB for duplicate SKUs in other inventories
        $duplicateSku = InventoryVariation::whereIn('sku', $skus)
            ->where('inventory_id', '!=', $inventory->id)
            ->first();

        if ($duplicateSku) {
            return back()->withInput()->with('error', "The SKU '{$duplicateSku->sku}' already exists in another inventory!");
        }
    }

    // ✅ Update inventory
    $inventory->update($data);

    // ✅ Update or create product
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

    // ✅ Delete removed variations
    if ($request->has('delete_variations') && !empty($request->delete_variations)) {
        $variationsToDelete = InventoryVariation::whereIn('id', $request->delete_variations)->get();
        foreach ($variationsToDelete as $variation) {
            if ($variation->image_path) {
                Storage::disk('public')->delete($variation->image_path);
            }
        }
        InventoryVariation::whereIn('id', $request->delete_variations)->delete();
    }

    // ✅ Update or create variations
    if (!empty($data['variations'])) {
        foreach ($data['variations'] as $variation) {
            $imagePath = null;
            if (isset($variation['image_path']) && $variation['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $variation['image_path']->store('variations', 'public');
            }

            // ✅ Generate unique SKU if empty
            $sku = !empty($variation['sku']) ? strtoupper($variation['sku']) : null;

            if (!$sku) {
                do {
                    $sku = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));
                } while (
                    InventoryVariation::where('sku', $sku)
                        ->where('inventory_id', $inventory->id)
                        ->exists()
                );
            }

            $variationData = [
                'sku' => $sku,
                'color' => $variation['color'] ?? null,
                'size' => $variation['size'] ?? null,
                'material' => $variation['material'] ?? null,
                'stock' => $variation['stock'] ?? 0,
                'price' => $variation['price'] ?? 0,
                'image_path' => $imagePath ?? ($variation['old_image'] ?? null),
            ];

            if (!empty($variation['id'])) {
                $invVar = $inventory->variations()->find($variation['id']);
                if ($invVar) {
                    // ✅ Delete old image if uploading a new one
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

        return redirect()->route('admin.inventory.index')
            ->with('success', "Inventory status updated to {$inventory->status}");
    }
}
