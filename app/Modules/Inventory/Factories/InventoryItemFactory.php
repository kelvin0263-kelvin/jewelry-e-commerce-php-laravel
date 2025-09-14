<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\InventoryItem;
use Exception;

class InventoryItemFactory
{
    public static function resolve(string $type): InventoryFactory
    {
        return match ($type) {
            'RingItem' => new RingItemFactory(),
            'NecklaceItem' => new NecklaceItemFactory(),
            'EarringsItem' => new EarringsItemFactory(),
            'BraceletItem' => new BraceletItemFactory(),
            default => throw new Exception("Unknown factory type: $type"),
        };
    }

    public static function create(string $type, string $material, float $price, array $inventoryData = []): InventoryItem
    {
        $factory = self::resolve($type);
        return $factory->create($material, $price, $inventoryData);
    }
}