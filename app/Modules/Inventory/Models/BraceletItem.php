<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Inventory\Models\InventoryItem;

class BraceletItem extends InventoryItem
{
    private string $claspType;
    private bool $adjustable;

    public function __construct(string $material, float $price, string $claspType, bool $adjustable = false)
    {
        parent::__construct($material, $price);
        $this->claspType = $claspType;
        $this->adjustable = $adjustable;
        $this->type = 'Bracelet';
    }

    public function getDescription(): string
    {
        $adjustableText = $this->adjustable ? 'adjustable' : 'fixed size';
        return "{$this->material} Bracelet with {$this->claspType} clasp, {$adjustableText}";
    }

    public function calculateValue(): float
    {
        $basePrice = $this->basePrice ?? 50;

        $claspPremiums = [
            'Standard' => 0,
            'Magnetic' => 50,
            'Toggle' => 30,
            'Lobster' => 20,
            'Box' => 25,
        ];

        $premium = $claspPremiums[$this->claspType] ?? 0;

        return $basePrice + $premium;
    }

}
