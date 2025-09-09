<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Inventory\Models\InventoryItem;

class EarringsItem extends InventoryItem
{
    private string $style;
    private bool $isPair;

    public function __construct(string $material, float $price, string $style, bool $isPair = true)
    {
        parent::__construct($material, $price);
        $this->style = $style;
        $this->isPair = $isPair;
        $this->type = 'Earrings';
    }

    public function getDescription(): string
    {
        $pairText = $this->isPair ? 'Pair of' : 'Single';
        return "{$pairText} {$this->material} {$this->style} Earrings";
    }

    public function calculateValue(): float
    {
        $basePrice = $this->basePrice ?? 400;

        $stylePremiums = [
            'Stud' => 0,
            'Hoop' => 120,
            'Drop' => 140,
            'Chandelier' => 180,
            'Cluster' => 150,
            'Dangle' => 200,
        ];

        $premium = $stylePremiums[$this->style] ?? 0;

        $price = $this->isPair ? $basePrice : $basePrice * 0.6;

        return $price + $premium;
    }

}
