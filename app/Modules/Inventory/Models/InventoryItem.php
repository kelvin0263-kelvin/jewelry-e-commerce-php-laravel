<?php

namespace App\Modules\Inventory\Models;

abstract class InventoryItem
{
    protected string $type;
    protected string $material;
    protected float $price;

    public function __construct(string $material, float $price)
    {
        $this->material = $material;
        $this->price = $price;
    }

    abstract public function getDescription(): string;
    abstract public function calculateValue(): float;
}
