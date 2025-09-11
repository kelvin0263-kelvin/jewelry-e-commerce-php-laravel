<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    // GET /api/inventory
    public function index()
    {
        $inventories = Inventory::with('variations')->latest()->get()->map(function($inv) {
            return $this->formatInventory($inv);
        });

        return response()->json(['success' => true, 'data' => $inventories]);
    }

    // GET /api/inventory/{id}
    public function show($id)
    {
        $inventory = Inventory::with('variations')->find($id);

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'error' => "Inventory with ID {$id} not found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatInventory($inventory)
        ]);
    }

    // POST /api/inventory
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
            'description' => 'nullable|string',
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
        ]);

        // Create inventory
        $inventory = Inventory::create($data);

        // Handle variations
        $this->handleVariations($inventory, $data['variations'] ?? []);

        // Reload variations to include in response
        $inventory->load('variations');

        return response()->json([
            'success' => true,
            'data' => $this->formatInventory($inventory)
        ], 201);
    }


    // PUT /api/inventory/{id}
    public function update(Request $request, $id)
{
    $inventory = Inventory::with('variations')->find($id);
    if (!$inventory) {
        return response()->json(['success' => false, 'error' => 'Inventory not found'], 404);
    }

    $data = $request->validate([
        'name' => 'sometimes|required|string',
        'type' => 'sometimes|required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
        'description' => 'nullable|string',
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
        'variations.*.id' => 'nullable|integer|exists:inventory_variations,id',
        'variations.*.sku' => 'nullable|string|max:50',
        'variations.*.color' => 'nullable|string|max:50',
        'variations.*.size' => 'nullable|string|max:50',
        'variations.*.material' => 'nullable|string|max:100',
        'variations.*.stock' => 'nullable|integer|min:0',
    ]);

    $inventory->update($data);

    // Handle variations
    $this->handleVariations($inventory, $data['variations'] ?? []);

    // Reload variations to include updated ones
    $inventory->load('variations');

    return response()->json([
        'success' => true,
        'data' => $this->formatInventory($inventory)
    ]);
}


    // DELETE /api/inventory/{id}
    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        if (!$inventory) {
            return response()->json(['success' => false, 'error' => 'Inventory not found'], 404);
        }

        $inventory->delete();
        return response()->json(['success' => true, 'message' => 'Inventory deleted successfully']);
    }

    /**
     * Format inventory with type-specific attributes and variations
     */
    protected function formatInventory($inv)
    {
        $data = [
            'id' => $inv->id,
            'name' => $inv->name,
            'type' => $inv->type,
            'description' => $inv->description,
            'status' => $inv->status,
            'quantity' => $inv->variations->sum('stock'),
            'variations' => $inv->variations->map(function($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'color' => $v->color,
                    'size' => $v->size,
                    'material' => $v->material,
                    'stock' => $v->stock,
                    'price' => $v->price,
                ];
            }),
        ];

        switch ($inv->type) {
            case 'RingItem':
                $data['stone_type'] = $inv->stone_type;
                $data['ring_size'] = $inv->ring_size;
                break;
            case 'NecklaceItem':
                $data['necklace_length'] = $inv->necklace_length;
                $data['has_pendant'] = $inv->has_pendant;
                break;
            case 'EarringsItem':
                $data['earring_style'] = $inv->earring_style;
                $data['is_pair'] = $inv->is_pair;
                break;
            case 'BraceletItem':
                $data['bracelet_clasp'] = $inv->bracelet_clasp;
                $data['adjustable'] = $inv->adjustable;
                break;
        }

        return $data;
    }

    /**
     * Handle creating/updating variations using the factory pattern
     */
   protected function handleVariations($inventory, $variations)
{
    if (empty($variations)) return;

    foreach ($variations as $var) {
        $item = $inventory->createInventoryItem($var);

        $sku = $var['sku'] ?? 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));

        if (!empty($var['id'])) {
            // Update existing variation
            $variation = $inventory->variations()->find($var['id']);
            if ($variation) {
                $variation->update([
                    'sku' => $sku,
                    'color' => $var['color'] ?? $variation->color,
                    'size' => $var['size'] ?? $variation->size,
                    'material' => $var['material'] ?? $variation->material,
                    'stock' => $var['stock'] ?? $variation->stock,
                    'price' => $item->calculateValue(),
                    'properties' => [
                        'description' => $item->getDescription(),
                        'calculated_value' => $item->calculateValue(),
                        'variation_data' => $var,
                    ],
                ]);
            }
        } else {
            // Create new variation
            $inventory->variations()->create([
                'sku' => $sku,
                'color' => $var['color'] ?? null,
                'size' => $var['size'] ?? null,
                'material' => $var['material'] ?? null,
                'stock' => $var['stock'] ?? 0,
                'price' => $item->calculateValue(),
                'properties' => [
                    'description' => $item->getDescription(),
                    'calculated_value' => $item->calculateValue(),
                    'variation_data' => $var,
                ],
            ]);
        }
    }
}

}