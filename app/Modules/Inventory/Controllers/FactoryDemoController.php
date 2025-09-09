<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use Illuminate\Http\Request;

class FactoryDemoController extends Controller
{
    /**
     * Demo method showing how to use the factory pattern with the new structure
     */
    public function demo()
    {
        // Example 1: Create a Ring inventory with type-specific attributes
        $ringData = [
            'name' => 'Diamond Ring Collection',
            'type' => 'RingItem',
            'description' => 'Beautiful diamond rings with various settings',
            'price' => 1500.00,
            'quantity' => 0,
            'min_stock_level' => 5,
            'status' => 'draft',
            // Type-specific attributes for RingItem
            'stone_type' => 'Diamond',
            'ring_size' => 7,
            'variations' => [
                [
                    'sku' => 'RING-001',
                    'color' => 'Gold',
                    'size' => '7',
                    'material' => '18K Gold',
                    'price' => 1500.00,
                    'stock' => 10,
                ],
                [
                    'sku' => 'RING-002',
                    'color' => 'Silver',
                    'size' => '8',
                    'material' => 'Sterling Silver',
                    'price' => 800.00,
                    'stock' => 15,
                ],
            ]
        ];

        // Example 2: Create a Necklace inventory with type-specific attributes
        $necklaceData = [
            'name' => 'Pearl Necklace Collection',
            'type' => 'NecklaceItem',
            'description' => 'Elegant pearl necklaces in various lengths',
            'price' => 1200.00,
            'quantity' => 0,
            'min_stock_level' => 3,
            'status' => 'draft',
            // Type-specific attributes for NecklaceItem
            'necklace_length' => 45,
            'has_pendant' => true,
            'variations' => [
                [
                    'sku' => 'NECK-001',
                    'color' => 'White',
                    'material' => 'Pearl',
                    'price' => 1200.00,
                    'stock' => 8,
                ],
                [
                    'sku' => 'NECK-002',
                    'color' => 'Black',
                    'material' => 'Black Pearl',
                    'price' => 1800.00,
                    'stock' => 5,
                ],
            ]
        ];

        // Example 3: Create Earrings inventory with type-specific attributes
        $earringsData = [
            'name' => 'Designer Earrings Collection',
            'type' => 'EarringsItem',
            'description' => 'Stylish earrings in various designs',
            'price' => 600.00,
            'quantity' => 0,
            'min_stock_level' => 4,
            'status' => 'draft',
            // Type-specific attributes for EarringsItem
            'earring_style' => 'Stud',
            'is_pair' => true,
            'variations' => [
                [
                    'sku' => 'EARR-001',
                    'color' => 'Gold',
                    'material' => '14K Gold',
                    'price' => 600.00,
                    'stock' => 12,
                ],
                [
                    'sku' => 'EARR-002',
                    'color' => 'Silver',
                    'material' => 'Sterling Silver',
                    'price' => 300.00,
                    'stock' => 20,
                ],
            ]
        ];

        // Example 4: Create Bracelet inventory with type-specific attributes
        $braceletData = [
            'name' => 'Luxury Bracelet Collection',
            'type' => 'BraceletItem',
            'description' => 'Premium bracelets with various clasp types',
            'price' => 900.00,
            'quantity' => 0,
            'min_stock_level' => 6,
            'status' => 'draft',
            // Type-specific attributes for BraceletItem
            'bracelet_clasp' => 'Magnetic',
            'adjustable' => true,
            'variations' => [
                [
                    'sku' => 'BRAC-001',
                    'color' => 'Gold',
                    'material' => '18K Gold',
                    'price' => 900.00,
                    'stock' => 8,
                ],
                [
                    'sku' => 'BRAC-002',
                    'color' => 'Silver',
                    'material' => 'Sterling Silver',
                    'price' => 450.00,
                    'stock' => 15,
                ],
            ]
        ];

        $results = [];

        try {
            // Create all inventories using factory pattern
            $results['ring'] = Inventory::create($ringData);
            $results['necklace'] = Inventory::create($necklaceData);
            $results['earrings'] = Inventory::create($earringsData);
            $results['bracelet'] = Inventory::create($braceletData);

            // Create variations for each inventory
            foreach ($results as $type => $inventory) {
                $variationData = ${$type . 'Data'}['variations'];
                foreach ($variationData as $variation) {
                    $item = $inventory->createInventoryItem($variation);
                    
                    $inventory->variations()->create([
                        'sku' => $variation['sku'],
                        'color' => $variation['color'],
                        'size' => $variation['size'] ?? null,
                        'material' => $variation['material'],
                        'price' => $item->calculateValue(),
                        'stock' => $variation['stock'],
                        'properties' => [
                            'description' => $item->getDescription(),
                            'calculated_value' => $item->calculateValue(),
                            'variation_data' => $variation,
                            'factory_created_at' => now()->toISOString(),
                        ],
                    ]);
                }
            }

            return response()->json([
                'message' => 'All inventories created successfully using factory pattern',
                'data' => $results,
                'factory_benefits' => [
                    'Type-specific attributes stored in inventory record',
                    'Factory pattern applied to variations',
                    'Automatic value calculations based on type',
                    'Enhanced descriptions generated',
                    'Type-specific data preserved in properties',
                    'Consistent object creation across all jewelry types'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create inventories: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Demo method showing how to get enhanced data
     */
    public function getEnhancedData($inventoryId)
    {
        $inventory = Inventory::with('variations')->find($inventoryId);
        
        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        return response()->json([
            'inventory' => $inventory,
            'factory_enhanced_features' => [
                'calculated_value' => $inventory->getCalculatedValue(),
                'item_description' => $inventory->getItemDescription(),
                'type_options' => $inventory->getTypeOptions(),
                'type_validation' => $inventory->validateTypeData(),
            ],
            'variations_with_factory_data' => $inventory->variations->map(function ($variation) {
                return [
                    'id' => $variation->id,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'properties' => $variation->properties,
                    'factory_description' => $variation->properties['description'] ?? 'No description',
                    'factory_calculated_value' => $variation->properties['calculated_value'] ?? $variation->price,
                ];
            }),
        ]);
    }

    /**
     * Demo method showing type-specific options
     */
    public function getTypeOptions($type)
    {
        $options = \App\Modules\Inventory\Factories\InventoryItemFactory::getTypeOptions($type);
        
        return response()->json([
            'type' => $type,
            'available_options' => $options,
            'validation_rules' => \App\Modules\Inventory\Factories\InventoryItemFactory::getValidationRules($type),
        ]);
    }
}


