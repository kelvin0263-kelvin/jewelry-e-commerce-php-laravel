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
        $inventories = Inventory::with('variations')->latest()->get();
        return response()->json($inventories);
    }

    // GET /api/inventory/{id}
    public function show($id)
    {
        $inventory = Inventory::with('variations')->find($id);
        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }
        return response()->json($inventory);
    }

    // POST /api/inventory
    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'type' => 'required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
        'description' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'quantity' => 'nullable|integer',
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
        'variations.*.price' => 'nullable|numeric|min:0',
        'variations.*.stock' => 'nullable|integer|min:0',
    ]);

    $inventory = Inventory::create($data);

    // Handle variations using factory pattern
    if (!empty($data['variations'])) {
        foreach ($data['variations'] as $variation) {
            // Create inventory item using factory pattern
            $item = $inventory->createInventoryItem($variation);

            $inventory->variations()->create([
                'sku' => $variation['sku'] ?? 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'color' => $variation['color'] ?? null,
                'size' => $variation['size'] ?? null,
                'material' => $variation['material'] ?? null,
                'price' => $item->calculateValue(), // Use factory-calculated value
                'stock' => $variation['stock'] ?? 0,
                'properties' => [
                    'description' => $item->getDescription(),
                    'calculated_value' => $item->calculateValue(),
                    'variation_data' => $variation,
                    'factory_created_at' => now()->toISOString(),
                ],
            ]);
        }
    }

    return response()->json($inventory->load('variations'), 201);
}


    // PUT /api/inventory/{id}
    public function update(Request $request, $id)
{
    $inventory = Inventory::with('variations')->find($id);
    if (!$inventory) {
        return response()->json(['error' => 'Inventory not found'], 404);
    }

    $data = $request->validate([
        'name' => 'sometimes|required|string',
        'type' => 'sometimes|required|in:RingItem,NecklaceItem,EarringsItem,BraceletItem',
        'description' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'quantity' => 'nullable|integer',
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
        'variations.*.price' => 'nullable|numeric|min:0',
        'variations.*.stock' => 'nullable|integer|min:0',
    ]);

    $inventory->update($data);

    // Handle variations using factory pattern
    if (!empty($data['variations'])) {
        foreach ($data['variations'] as $var) {
            if (!empty($var['id'])) {
                // Update existing variation
                $variation = $inventory->variations()->find($var['id']);
                if ($variation) {
                    // Create inventory item using factory pattern
                    $item = $inventory->createInventoryItem($var);
                    
                    $variation->update([
                        'sku' => $var['sku'] ?? $variation->sku,
                        'color' => $var['color'] ?? $variation->color,
                        'size' => $var['size'] ?? $variation->size,
                        'material' => $var['material'] ?? $variation->material,
                        'price' => $item->calculateValue(), // Use factory-calculated value
                        'stock' => $var['stock'] ?? $variation->stock,
                        'properties' => [
                            'description' => $item->getDescription(),
                            'calculated_value' => $item->calculateValue(),
                            'variation_data' => $var,
                            'factory_updated_at' => now()->toISOString(),
                        ],
                    ]);
                }
            } else {
                // Create new variation
                $item = $inventory->createInventoryItem($var);
                
                $inventory->variations()->create([
                    'sku' => $var['sku'] ?? 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    'color' => $var['color'] ?? null,
                    'size' => $var['size'] ?? null,
                    'material' => $var['material'] ?? null,
                    'price' => $item->calculateValue(), // Use factory-calculated value
                    'stock' => $var['stock'] ?? 0,
                    'properties' => [
                        'description' => $item->getDescription(),
                        'calculated_value' => $item->calculateValue(),
                        'variation_data' => $var,
                        'factory_created_at' => now()->toISOString(),
                    ],
                ]);
            }
        }
    }

    return response()->json($inventory->load('variations'));
}

    // DELETE /api/inventory/{id}
    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
