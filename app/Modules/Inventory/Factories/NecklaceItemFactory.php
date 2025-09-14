<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\NecklaceItem;
use App\Modules\Inventory\Models\InventoryItem;

class NecklaceItemFactory implements InventoryFactory
{
    public function create(string $material, float $price, array $inventoryData = []): InventoryItem
    {
        // Use variation data if available, otherwise fall back to inventory data
        $length = (int)($inventoryData['necklace_length'] ?? 45);
        $hasPendant = (bool)($inventoryData['has_pendant'] ?? false);
        
        return new NecklaceItem(
            $material,
            $price,
            $length,
            $hasPendant
        );
    }

    public function getValidationRules(): array
    {
        return [
            'necklace_length' => 'required|integer|min:30|max:80',
            'has_pendant'     => 'required|boolean',
        ];
    }

    public function getTypeOptions(): array
    {
        return [
            'necklace_lengths' => [30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80],
            'pendant_options'  => [true, false],
        ];
    }

    public function validateTypeData(array $data): array
    {
        $errors = [];
        foreach ($this->getValidationRules() as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required for NecklaceItem";
            }
        }
        return $errors;
    }
}
