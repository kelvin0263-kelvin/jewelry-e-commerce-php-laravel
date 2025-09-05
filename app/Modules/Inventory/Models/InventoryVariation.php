<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryVariation extends Model
{
    use HasFactory;

    protected $table = 'inventory_variations';

    protected $fillable = [
        'sku',
        'inventory_id',
        'color',
        'size',
        'material',
        'price',
        'stock',
        'image_path',
    ];

    // Each variation belongs to one inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}