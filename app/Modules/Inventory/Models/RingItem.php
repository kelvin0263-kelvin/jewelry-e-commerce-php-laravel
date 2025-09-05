<?php

namespace App\Modules\Inventory\Models;

class RingItem extends InventoryItem
{
    private string $stoneType;
    private int $size;

    public function __construct(string $material, float $price, string $stoneType, int $size)
    {
        parent::__construct($material, $price);
        $this->stoneType = $stoneType;
        $this->size = $size;
        $this->type = 'Ring';
    }

    public function getDescription(): string
    {
        return "{$this->material} Ring with {$this->stoneType} stone, Size {$this->size}";
    }

    public function calculateValue(): float
    {
        $stonePremium = $this->stoneType === 'Diamond' ? 500 : 100;
        return $this->price + $stonePremium;
    }
}