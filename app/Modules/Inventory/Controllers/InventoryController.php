<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Models\Product; // <-- Add this line
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::latest()->paginate(15);
        return view('inventory::admin.inventory.index', compact('inventories'));
    }

    public function create()
    {
        return view('inventory::admin.inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
            'quantity' => 'nullable|integer',
            'min_stock_level' => 'nullable|integer',
            'status' => 'nullable|in:draft,published',
            'variations' => 'nullable|array',
        ]);

        $inventory = Inventory::create($data);

        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $inventory->variations()->create([
                    'sku' => $variation['sku'] ?? 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    'material' => $variation['material'] ?? null,
                    'stock' => $variation['stock'] ?? 0,
                    'price' => $variation['price'] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory created successfully');
    }

    public function edit($id)
    {
        $inventory = Inventory::with('variations')->findOrFail($id);
        return view('inventory::admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, $id)
{
    $inventory = Inventory::findOrFail($id);

    // Validate input
    $data = $request->validate([
        'name' => 'sometimes|string',
        'type' => 'sometimes|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
        'quantity' => 'nullable|integer',
        'min_stock_level' => 'nullable|integer',
        'status' => 'nullable|in:draft,published',
        'variations' => 'nullable|array',
    ]);

    // Update inventory
    $inventory->update($data);

    // Update or create variations safely
    if (!empty($data['variations'])) {
        foreach ($data['variations'] as $variation) {

            $variationData = [
                'sku' => $variation['sku'] ?? 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'material' => $variation['material'] ?? null,
                'stock' => $variation['stock'] ?? 0,
                'price' => $variation['price'] ?? 0,
            ];

            if (isset($variation['id'])) {
                $invVar = $inventory->variations()->find($variation['id']);
                if ($invVar) {
                    $invVar->update($variationData);
                }
            } else {
                $inventory->variations()->create($variationData);
            }
        }
    }

    return redirect()->route('admin.inventory.index')
        ->with('success', 'Inventory updated successfully');
}

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->variations()->delete();
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory deleted successfully');
    }

    // Only toggle inventory status
    public function toggleStatus($id)
{
    $inventory = Inventory::findOrFail($id);

    // Toggle status
    $inventory->status = $inventory->status === 'published' ? 'draft' : 'published';
    $inventory->save();

    // Automatically create or update the product
    $product = $inventory->product;

if ($inventory->status === 'published') {
    if ($product) {
        $product->update(['status' => 'published']);
    } else {
        Product::create([
            'inventory_id' => $inventory->id,
            'name' => $inventory->name,
            'price' => 0,
            'status' => 'published',
        ]);
    }
} else {
    if ($product) {
        $product->update(['status' => 'draft']);
    }
}

    return redirect()->route('admin.inventory.index')
        ->with('success', "Inventory status updated to {$inventory->status}");
}
}

