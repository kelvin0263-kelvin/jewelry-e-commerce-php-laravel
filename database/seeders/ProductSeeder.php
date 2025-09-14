<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have at least one admin user for published_by / issued_by
        $admin = User::where('is_admin', true)->first();
        $now = Carbon::now();

        // Source directory with provided images
        $sourceDir = public_path('img/curved-images');

        // Accepted extensions (some files are webp/avif per request)
        $exts = ['webp', 'avif', 'jpg', 'jpeg', 'png'];

        // Rotate through inventory types for the 5 products
        $types = ['NecklaceItem', 'RingItem', 'EarringsItem', 'NecklaceItem', 'EarringsItem'];


        $names = [
            'Tiffany Lock Small Pendant in Rose Gold',
            'Sixteen Stone by Tiffany Ring with Diamonds and Sapphires',
            'Tiffany Knot Drop Earrings in Yellow Gold with Diamonds',
            'Swarovski Women Metallic Symbolica Rhodium Plated Star Pendant Necklace',
            'Bella V drop earrings Round cut, Pink, Rhodium plated',
        ];

        for ($i = 1; $i <= 5; $i++) {
            // Create a dedicated inventory per product (published so it appears live)
            $name = $names[$i - 1]; // pick the corresponding name
            $inventory = Inventory::updateOrCreate(
                ['name' => $name],
                [
                    'type' => $types[$i - 1],
                    'description' => 'Sample seeded product #' . $i,
                    'price' => 100 + ($i - 1) * 50,
                    'quantity' => 5,
                    'min_stock_level' => 1,
                    'status' => 'published',
                ]
            );

            // Build the 5-image set for this product: productimg{start..end}.{ext}
            $start = ($i - 1) * 5 + 1; // 1,6,11,16,21
            $end = $start + 4;         // 5,10,15,20,25
            $destBase = 'products/product' . $i; // storage/app/public/products/product{i}
            if (!Storage::disk('public')->exists($destBase)) {
                Storage::disk('public')->makeDirectory($destBase);
            }

            $copied = [];
            for ($n = $start; $n <= $end; $n++) {
                $foundPath = null;
                $foundName = null;
                foreach ($exts as $ext) {
                    $candidate = 'productimg' . $n . '.' . $ext;
                    $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $candidate;
                    if (File::exists($sourcePath)) {
                        $foundPath = $sourcePath;
                        $foundName = $candidate;
                        break;
                    }
                }

                if ($foundPath && $foundName) {
                    $targetPath = $destBase . '/' . $foundName; // relative to storage/public
                    // Copy into storage/public so views using asset('storage/...') work
                    Storage::disk('public')->put($targetPath, File::get($foundPath));
                    $copied[] = $targetPath;
                }
            }

            // If no images found for this product range, skip creation
            if (count($copied) === 0) {
                continue;
            }

            // Create a single variation (SKU) so product listing can find stock
            $sku = 'SKU-P' . $i . '-001';
            $variation = InventoryVariation::updateOrCreate(
                ['sku' => $sku],
                [
                    'inventory_id' => $inventory->id,
                    'color' => 'Default',
                    'size' => null,
                    'material' => 'Mixed',
                    'price' => $inventory->price,
                    'stock' => 10,
                    'image_path' => $copied[0] ?? null,
                ]
            );

            // Create/update the product tied to this inventory + variation
            Product::updateOrCreate(
                ['inventory_id' => $inventory->id, 'inventory_variation_id' => $variation->id],
                [
                    'name' => $name,
                    'description' => "Beautiful jewelry item: $name with curated images.",
                    'marketing_description' => null,
                    'price' => $variation->price,
                    'selling_price' => $variation->price,
                    'discount_price' => null,
                    'sku' => $variation->sku,
                    'product_id' => null,
                    'image_path' => $copied[0] ?? null, // main image = first of the 5
                    'customer_images' => $copied,       // exactly the copied set
                    'product_video' => null,
                    'status' => 'published',
                    'category' => $inventory->type,
                    'features' => [
                        'image_count' => count($copied),
                        'set_range' => $start . '-' . $end,
                    ],
                    'published_by' => $admin?->id,
                    'published_at' => $now,
                    'is_visible' => true,
                    'issued_at' => null,
                    'issued_by' => null,
                ]
            );
        }
    }
}
