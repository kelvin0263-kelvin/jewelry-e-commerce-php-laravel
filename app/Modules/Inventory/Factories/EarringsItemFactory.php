<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\EarringsItem;
use App\Modules\Inventory\Models\InventoryItem;

class EarringsItemFactory implements InventoryFactory
{
    public function create(string $material, float $price, array $inventoryData = []): InventoryItem
    {
        // Use variation data if available, otherwise fall back to inventory data
        $style = $inventoryData['earring_style'] ?? 'Stud';
        $isPair = (bool) ($inventoryData['is_pair'] ?? true);

        return new EarringsItem(
            $material,
            $price,
            $style,
            $isPair
        );
    }

    public function getValidationRules(): array
    {
        return [
            'earring_style' => 'required|string|max:50',
            'is_pair' => 'required|boolean',
        ];
    }

    public function getTypeOptions(): array
    {
        return [
            'earring_styles' => ['Stud', 'Hoop', 'Drop', 'Chandelier', 'Cluster', 'Dangle'],
            'pair_options' => [true, false],
        ];
    }

    public function validateTypeData(array $data): array
    {
        $errors = [];
        foreach ($this->getValidationRules() as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required for EarringsItem";
            }
        }
        return $errors;
    }
}
