<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Factories\InventoryFactory;

class InventoryService
{
    public function getAll()
    {
        return Inventory::latest()->paginate(10);
    }

    public function findById($id)
    {
        return Inventory::findOrFail($id);
    }

    public function create(array $data)
    {
        // Use the factory to build a new Inventory
        $inventory = InventoryFactory::create($data);
        $inventory->save();
        return $inventory;
    }

    public function update(Inventory $inventory, array $data)
    {
        $inventory->update($data);
        return $inventory;
    }

    public function delete(Inventory $inventory)
    {
        return $inventory->delete();
    }
}
