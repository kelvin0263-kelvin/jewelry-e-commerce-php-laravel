<?php

namespace App\Modules\Inventory\Factories;

use App\Modules\Inventory\Models\InventoryItem;

interface InventoryFactory
{
    public function create(string $material, float $price, array $inventoryData = []): InventoryItem;
    public function getValidationRules(): array;
    public function getTypeOptions(): array;
    public function validateTypeData(array $data): array;
}