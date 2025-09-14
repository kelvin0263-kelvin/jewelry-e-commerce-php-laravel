<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\RingItem;
use App\Modules\Inventory\Models\InventoryItem;

class RingItemFactory implements InventoryFactory
{
    public function create(string $material, float $price, array $inventoryData = []): InventoryItem
    {
        // Use variation data if available, otherwise fall back to inventory data
        $stoneType = $inventoryData['material'] ?? $inventoryData['stone_type'] ?? 'Diamond';
        $ringSize = (int) ($inventoryData['size'] ?? $inventoryData['ring_size'] ?? 7);

        return new RingItem(
            $material,
            $price,
            $stoneType,
            $ringSize
        );
    }

    public function getValidationRules(): array
    {
        return [
            'stone_type' => 'required|string|max:50',
            'ring_size' => 'required|integer|min:4|max:12',
        ];
    }

    public function getTypeOptions(): array
    {
        return [
            'stone_types' => ['Diamond', 'Ruby', 'Sapphire', 'Emerald', 'Pearl', 'Amethyst'],
            'ring_sizes' => range(4, 12),
        ];
    }

    public function validateTypeData(array $data): array
    {
        $errors = [];
        $rules = $this->getValidationRules();

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required for RingItem";
            }
        }
        return $errors;
    }
}