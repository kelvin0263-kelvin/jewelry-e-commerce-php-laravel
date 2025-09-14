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
        $stonePremium = self::getStonePremium($this->stoneType);

        return $basePrice + $stonePremium;
    }

    
    //Get stone premium for a given stone type
    public static function getStonePremium(string $stoneType): int
    {
        $stonePremiums = [
            'Diamond' => 500,
            'Ruby' => 300,
            'Sapphire' => 250,
            'Emerald' => 200,
            'Pearl' => 150,
            'Amethyst' => 100,
        ];

        return $stonePremiums[$stoneType] ?? 0;
    }

    //Get all available stone types with their premiums
    public static function getStoneTypes(): array
    {
        return [
            'Diamond' => 500,
            'Ruby' => 300,
            'Sapphire' => 250,
            'Emerald' => 200,
            'Pearl' => 150,
            'Amethyst' => 100,
        ];
    }



}