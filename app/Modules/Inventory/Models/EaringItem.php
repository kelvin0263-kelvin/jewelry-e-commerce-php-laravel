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
        return $this->isPair ? $this->price : $this->price * 0.6;
    }
}
