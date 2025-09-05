<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Inventory\Models\InventoryItem;

class NecklaceItem extends InventoryItem
{
    private int $length;
    private bool $hasPendant;

    public function __construct(string $material, float $price, int $length, bool $hasPendant = false)
    {
        parent::__construct($material, $price);
        $this->length = $length;
        $this->hasPendant = $hasPendant;
        $this->type = 'Necklace';
    }

    public function getDescription(): string
    {
        $pendantText = $this->hasPendant ? 'with pendant' : 'without pendant';
        return "{$this->material} Necklace, {$this->length}cm, {$pendantText}";
    }

    public function calculateValue(): float
    {
        $lengthPremium = $this->length * 10;
        $pendantPremium = $this->hasPendant ? 200 : 0;
        return $this->price + $lengthPremium + $pendantPremium;
    }
}
