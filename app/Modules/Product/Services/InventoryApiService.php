<?php

namespace App\Modules\Product\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryApiService
{
    /**
     * Fetch inventory data from Inventory API
     * GET /api/inventory/{id}
     */
    public function fetchInventory(int $id): ?object
    {
        try {
            $base = config('services.inventory_api.base');
            $timeout = (float) config('services.inventory_api.timeout', 10);

            $res = Http::retry(2, 150)->timeout($timeout)->acceptJson()->get("$base/$id");
            if (!$res->ok()) {
                Log::warning('Inventory API returned non-200 status for inventory', ['id' => $id, 'status' => $res->status()]);
                return $this->getFallbackInventory($id);
            }

            $payload = $res->json();
            $inventory = $payload['data'] ?? $payload;

            return (object) [
                'id' => (int) ($inventory['id'] ?? $id),
                'name' => $inventory['name'] ?? '',
                'type' => $inventory['type'] ?? '',
                'description' => $inventory['description'] ?? '',
                'status' => $inventory['status'] ?? 'draft',
                'quantity' => (int) ($inventory['quantity'] ?? 0),
                'variations' => $inventory['variations'] ?? [],
                'stone_type' => $inventory['stone_type'] ?? null,
                'ring_size' => $inventory['ring_size'] ?? null,
                'necklace_length' => $inventory['necklace_length'] ?? null,
                'has_pendant' => $inventory['has_pendant'] ?? null,
                'earring_style' => $inventory['earring_style'] ?? null,
                'is_pair' => $inventory['is_pair'] ?? null,
                'bracelet_clasp' => $inventory['bracelet_clasp'] ?? null,
                'adjustable' => $inventory['adjustable'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch inventory from API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'url' => config('services.inventory_api.base')
            ]);
            return $this->getFallbackInventory($id);
        }
    }

    /**
     * Fetch all inventories from Inventory API
     * GET /api/inventory
     */
    public function fetchAllInventories(): array
    {
        try {
            $base = config('services.inventory_api.base');
            $timeout = (float) config('services.inventory_api.timeout', 10);

            $res = Http::retry(2, 150)->timeout($timeout)->acceptJson()->get($base);
            if (!$res->ok()) {
                Log::warning('Inventory API returned non-200 status', ['status' => $res->status()]);
                return $this->getFallbackInventories();
            }

            $payload = $res->json();
            $inventories = $payload['data'] ?? [];

            return array_map(function($inventory) {
                return (object) [
                    'id' => (int) $inventory['id'],
                    'name' => $inventory['name'] ?? '',
                    'type' => $inventory['type'] ?? '',
                    'description' => $inventory['description'] ?? '',
                    'status' => $inventory['status'] ?? 'draft',
                    'quantity' => (int) ($inventory['quantity'] ?? 0),
                    'variations' => $inventory['variations'] ?? [],
                    'stone_type' => $inventory['stone_type'] ?? null,
                    'ring_size' => $inventory['ring_size'] ?? null,
                    'necklace_length' => $inventory['necklace_length'] ?? null,
                    'has_pendant' => $inventory['has_pendant'] ?? null,
                    'earring_style' => $inventory['earring_style'] ?? null,
                    'is_pair' => $inventory['is_pair'] ?? null,
                    'bracelet_clasp' => $inventory['bracelet_clasp'] ?? null,
                    'adjustable' => $inventory['adjustable'] ?? null,
                ];
            }, $inventories);
        } catch (\Exception $e) {
            Log::error('Failed to fetch inventories from API', [
                'error' => $e->getMessage(),
                'url' => config('services.inventory_api.base')
            ]);
            return $this->getFallbackInventories();
        }
    }

    /**
     * Fetch inventory variation by SKU from Inventory API
     * This method searches through all inventories to find a variation with specific SKU
     */
    public function fetchVariationBySku(string $sku): ?object
    {
        $inventories = $this->fetchAllInventories();
        
        foreach ($inventories as $inventory) {
            foreach ($inventory->variations as $variation) {
                if ($variation['sku'] === $sku) {
                    return (object) [
                        'id' => (int) $variation['id'],
                        'sku' => $variation['sku'],
                        'color' => $variation['color'] ?? null,
                        'size' => $variation['size'] ?? null,
                        'material' => $variation['material'] ?? null,
                        'stock' => (int) ($variation['stock'] ?? 0),
                        'price' => (float) ($variation['price'] ?? 0),
                        'inventory_id' => $inventory->id,
                        'inventory' => $inventory,
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Fetch inventory variation by ID from Inventory API
     * This method searches through all inventories to find a variation with specific ID
     */
    public function fetchVariationById(int $id): ?object
    {
        try {
            $inventories = $this->fetchAllInventories();
            
            foreach ($inventories as $inventory) {
                foreach ($inventory->variations as $variation) {
                    if ((int) $variation['id'] === $id) {
                        return (object) [
                            'id' => (int) $variation['id'],
                            'sku' => $variation['sku'],
                            'color' => $variation['color'] ?? null,
                            'size' => $variation['size'] ?? null,
                            'material' => $variation['material'] ?? null,
                            'stock' => (int) ($variation['stock'] ?? 0),
                            'price' => (float) ($variation['price'] ?? 0),
                            'inventory_id' => $inventory->id,
                            'inventory' => $inventory,
                        ];
                    }
                }
            }
            
            // If not found in API data, try fallback
            return $this->getFallbackVariationById($id);
        } catch (\Exception $e) {
            Log::error('Failed to fetch variation by ID from API', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->getFallbackVariationById($id);
        }
    }

    /**
     * Fetch inventory variations by inventory ID
     */
    public function fetchVariationsByInventoryId(int $inventoryId): array
    {
        $inventory = $this->fetchInventory($inventoryId);
        if (!$inventory) return [];

        return array_map(function($variation) use ($inventory) {
            return (object) [
                'id' => (int) $variation['id'],
                'sku' => $variation['sku'],
                'color' => $variation['color'] ?? null,
                'size' => $variation['size'] ?? null,
                'material' => $variation['material'] ?? null,
                'stock' => (int) ($variation['stock'] ?? 0),
                'price' => (float) ($variation['price'] ?? 0),
                'inventory_id' => $inventory->id,
                'inventory' => $inventory,
            ];
        }, $inventory->variations);
    }

    /**
     * Check if inventory API is available
     */
    public function isApiAvailable(): bool
    {
        try {
            $base = config('services.inventory_api.base');
            $timeout = (float) config('services.inventory_api.timeout', 10);
            
            $res = Http::timeout($timeout)->get($base);
            return $res->ok();
        } catch (\Exception $e) {
            Log::warning('Inventory API not available', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Fallback method to get inventories from database when API is not available
     */
    private function getFallbackInventories(): array
    {
        try {
            // Use direct database access as fallback
            $inventories = \App\Modules\Inventory\Models\Inventory::with('variations')->get();
            
            return $inventories->map(function($inventory) {
                return (object) [
                    'id' => $inventory->id,
                    'name' => $inventory->name,
                    'type' => $inventory->type,
                    'description' => $inventory->description,
                    'status' => $inventory->status,
                    'quantity' => $inventory->variations->sum('stock'),
                    'variations' => $inventory->variations->map(function($variation) {
                        return [
                            'id' => $variation->id,
                            'sku' => $variation->sku,
                            'color' => $variation->color,
                            'size' => $variation->size,
                            'material' => $variation->material,
                            'stock' => $variation->stock,
                            'price' => $variation->price,
                        ];
                    })->toArray(),
                    'stone_type' => $inventory->stone_type,
                    'ring_size' => $inventory->ring_size,
                    'necklace_length' => $inventory->necklace_length,
                    'has_pendant' => $inventory->has_pendant,
                    'earring_style' => $inventory->earring_style,
                    'is_pair' => $inventory->is_pair,
                    'bracelet_clasp' => $inventory->bracelet_clasp,
                    'adjustable' => $inventory->adjustable,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Fallback inventory fetch also failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Fallback method to get single inventory from database when API is not available
     */
    private function getFallbackInventory(int $id): ?object
    {
        try {
            $inventory = \App\Modules\Inventory\Models\Inventory::with('variations')->find($id);
            
            if (!$inventory) {
                return null;
            }
            
            return (object) [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'type' => $inventory->type,
                'description' => $inventory->description,
                'status' => $inventory->status,
                'quantity' => $inventory->variations->sum('stock'),
                'variations' => $inventory->variations->map(function($variation) {
                    return [
                        'id' => $variation->id,
                        'sku' => $variation->sku,
                        'color' => $variation->color,
                        'size' => $variation->size,
                        'material' => $variation->material,
                        'stock' => $variation->stock,
                        'price' => $variation->price,
                    ];
                })->toArray(),
                'stone_type' => $inventory->stone_type,
                'ring_size' => $inventory->ring_size,
                'necklace_length' => $inventory->necklace_length,
                'has_pendant' => $inventory->has_pendant,
                'earring_style' => $inventory->earring_style,
                'is_pair' => $inventory->is_pair,
                'bracelet_clasp' => $inventory->bracelet_clasp,
                'adjustable' => $inventory->adjustable,
            ];
        } catch (\Exception $e) {
            Log::error('Fallback single inventory fetch also failed', ['id' => $id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fallback method to get variation by ID from database when API is not available
     */
    private function getFallbackVariationById(int $id): ?object
    {
        try {
            $variation = \App\Modules\Inventory\Models\InventoryVariation::with('inventory')->find($id);
            
            if (!$variation) {
                return null;
            }
            
            return (object) [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'color' => $variation->color,
                'size' => $variation->size,
                'material' => $variation->material,
                'stock' => $variation->stock,
                'price' => $variation->price,
                'inventory_id' => $variation->inventory_id,
                'inventory' => (object) [
                    'id' => $variation->inventory->id,
                    'name' => $variation->inventory->name,
                    'type' => $variation->inventory->type,
                    'description' => $variation->inventory->description,
                    'status' => $variation->inventory->status,
                    'quantity' => $variation->inventory->variations->sum('stock'),
                    'stone_type' => $variation->inventory->stone_type,
                    'ring_size' => $variation->inventory->ring_size,
                    'necklace_length' => $variation->inventory->necklace_length,
                    'has_pendant' => $variation->inventory->has_pendant,
                    'earring_style' => $variation->inventory->earring_style,
                    'is_pair' => $variation->inventory->is_pair,
                    'bracelet_clasp' => $variation->inventory->bracelet_clasp,
                    'adjustable' => $variation->inventory->adjustable,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Fallback variation fetch by ID also failed', ['id' => $id, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
