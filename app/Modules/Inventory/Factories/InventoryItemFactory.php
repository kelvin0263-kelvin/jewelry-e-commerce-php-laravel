<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\RingItem;
use App\Modules\Inventory\Models\NecklaceItem;
use App\Modules\Inventory\Models\EarringsItem;
use App\Modules\Inventory\Models\BraceletItem;
use App\Modules\Inventory\Models\InventoryItem;

class InventoryItemFactory
{
    /**
     * Create inventory item based on type and inventory data
     */
    public static function create(string $type, string $material, float $price, array $inventoryData = []): InventoryItem
    {
        return match ($type) {
            'RingItem' => new RingItem(
                $material, 
                $price, 
                $inventoryData['stone_type'] ?? 'Diamond', 
                (int)($inventoryData['ring_size'] ?? 7)
            ),
            'NecklaceItem' => new NecklaceItem(
                $material, 
                $price, 
                (int)($inventoryData['necklace_length'] ?? 45), 
                (bool)($inventoryData['has_pendant'] ?? false)
            ),
            'EarringsItem' => new EarringsItem(
                $material, 
                $price, 
                $inventoryData['earring_style'] ?? 'Stud', 
                (bool)($inventoryData['is_pair'] ?? true)
            ),
            'BraceletItem' => new BraceletItem(
                $material, 
                $price, 
                $inventoryData['bracelet_clasp'] ?? 'Standard', 
                (bool)($inventoryData['adjustable'] ?? false)
            ),
            default => throw new \Exception("Unknown inventory item type: $type"),
        };
    }

    /**
     * Get type-specific validation rules
     */
    public static function getValidationRules(string $type): array
    {
        return match ($type) {
            'RingItem' => [
                'stone_type' => 'required|string|max:50',
                'ring_size' => 'required|integer|min:4|max:12',
            ],
            'NecklaceItem' => [
                'necklace_length' => 'required|integer|min:30|max:80',
                'has_pendant' => 'required|boolean',
            ],
            'EarringsItem' => [
                'earring_style' => 'required|string|max:50',
                'is_pair' => 'required|boolean',
            ],
            'BraceletItem' => [
                'bracelet_clasp' => 'required|string|max:50',
                'adjustable' => 'required|boolean',
            ],
            default => [],
        };
    }

    /**
     * Get available options for each type
     */
    public static function getTypeOptions(string $type): array
    {
        return match ($type) {
            'RingItem' => [
                'stone_types' => ['Diamond', 'Ruby', 'Sapphire', 'Emerald', 'Pearl', 'Amethyst'],
                'ring_sizes' => range(4, 12),
            ],
            'NecklaceItem' => [
                'necklace_lengths' => [30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80],
                'pendant_options' => [true, false],
            ],
            'EarringsItem' => [
                'earring_styles' => ['Stud', 'Hoop', 'Drop', 'Chandelier', 'Cluster', 'Dangle'],
                'pair_options' => [true, false],
            ],
            'BraceletItem' => [
                'bracelet_clasps' => ['Standard', 'Magnetic', 'Toggle', 'Lobster', 'Box'],
                'adjustable_options' => [true, false],
            ],
            default => [],
        };
    }

    /**
     * Validate type-specific data
     */
    public static function validateTypeData(string $type, array $data): array
    {
        $errors = [];
        $rules = self::getValidationRules($type);

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required for {$type}";
            }
        }

        return $errors;
    }
}


