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
        $basePrice = $this->basePrice ?? 500;

        // Stone premium
        $stonePremiums = [
            'Diamond' => 500,
            'Ruby' => 300,
            'Sapphire' => 250,
            'Emerald' => 200,
            'Pearl' => 150,
            'Amethyst' => 100,
        ];
        $stonePremium = $stonePremiums[$this->stoneType] ?? 0;

        return $basePrice + $stonePremium;
    }



}