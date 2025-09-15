<?php
// Author: Ng Yanding
// Date: 2025-09-15
namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\BraceletItem;
use App\Modules\Inventory\Models\InventoryItem;

class BraceletItemFactory implements InventoryFactory
{
    public function create(string $material, float $price, array $inventoryData = []): InventoryItem
    {
        // Use variation data if available, otherwise fall back to inventory data
        $claspType = $inventoryData['bracelet_clasp'] ?? 'Standard';
        $adjustable = (bool) ($inventoryData['adjustable'] ?? false);

        return new BraceletItem(
            $material,
            $price,
            $claspType,
            $adjustable
        );
    }

    public function getValidationRules(): array
    {
        return [
            'bracelet_clasp' => 'required|string|max:50',
            'adjustable' => 'required|boolean',
        ];
    }

    public function getTypeOptions(): array
    {
        return [
            'bracelet_clasps' => ['Standard', 'Magnetic', 'Toggle', 'Lobster', 'Box'],
            'adjustable_options' => [true, false],
        ];
    }

    public function validateTypeData(array $data): array
    {
        $errors = [];
        foreach ($this->getValidationRules() as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required for BraceletItem";
            }
        }
        return $errors;
    }
}
