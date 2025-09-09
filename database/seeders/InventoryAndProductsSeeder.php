<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;

class InventoryAndProductsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Gold Ring',
                'type' => 'RingItem', // 与 migration 枚举一致
                'variations' => [
                    ['code' => 'YG-6', 'color' => 'Yellow Gold', 'size' => '6', 'material' => '18k Gold', 'price' => 299.99, 'stock' => 10],
                    ['code' => 'RG-7', 'color' => 'Rose Gold',   'size' => '7', 'material' => '18k Gold', 'price' => 319.99, 'stock' => 8],
                    ['code' => 'WG-8', 'color' => 'White Gold',  'size' => '8', 'material' => '18k Gold', 'price' => 329.99, 'stock' => 5],
                ],
            ],
            [
                'name' => 'Diamond Necklace',
                'type' => 'NecklaceItem',
                'variations' => [
                    ['code' => 'SL-40', 'color' => 'Silver', 'size' => '40cm', 'material' => 'Sterling Silver', 'price' => 499.99, 'stock' => 7],
                    ['code' => 'GL-45', 'color' => 'Gold',   'size' => '45cm', 'material' => '18k Gold',        'price' => 549.99, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Pearl Earrings',
                'type' => 'EarringsItem',
                'variations' => [
                    ['code' => 'FR-WH', 'color' => 'White', 'size' => null, 'material' => 'Freshwater Pearl', 'price' => 149.99, 'stock' => 12],
                    ['code' => 'AK-PK', 'color' => 'Pink',  'size' => null, 'material' => 'Akoya Pearl',      'price' => 189.99, 'stock' => 6],
                ],
            ],
            [
                'name' => 'Silver Bracelet',
                'type' => 'BraceletItem',
                'variations' => [
                    ['code' => 'SM-16', 'color' => 'Silver', 'size' => '16cm', 'material' => 'Sterling Silver', 'price' => 89.99,  'stock' => 10],
                    ['code' => 'MD-18', 'color' => 'Silver', 'size' => '18cm', 'material' => 'Sterling Silver', 'price' => 99.99,  'stock' => 10],
                    ['code' => 'LG-20', 'color' => 'Silver', 'size' => '20cm', 'material' => 'Sterling Silver', 'price' => 109.99, 'stock' => 8],
                ],
            ],
        ];

        foreach ($items as $index => $item) {
            $totalStock = array_sum(array_column($item['variations'], 'stock'));

            // Create or update the inventory
            $inventory = Inventory::updateOrCreate(
                ['name' => $item['name']],
                [
                    'type' => $item['type'],
                    'quantity' => $totalStock,
                    'min_stock_level' => 5,
                    'status' => 'published',
                ]
            );

            // Seed variations with deterministic SKUs
            foreach ($item['variations'] as $vIdx => $variation) {
                $skuPrefix = strtoupper(substr($item['type'], 0, 3));
                $sku = $skuPrefix . '-' . str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) . '-' . strtoupper($variation['code']);

                InventoryVariation::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'inventory_id' => $inventory->id,
                        'color' => $variation['color'],
                        'size' => $variation['size'],
                        'material' => $variation['material'],
                        'price' => $variation['price'],
                        'stock' => $variation['stock'],
                        'image_path' => null,
                    ]
                );
            }

            // Ensure a product exists and reflects a sensible price
            $minPrice = min(array_column($item['variations'], 'price'));
            Product::updateOrCreate(
                ['inventory_id' => $inventory->id],
                [
                    'name' => $item['name'],
                    'description' => $item['name'] . ' from our curated collection.',
                    'price' => $minPrice,
                    'image_path' => null,
                    'status' => 'published',
                ]
            );
        }
    }
}
