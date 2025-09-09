<?php

require_once 'vendor/autoload.php';

use App\Modules\Inventory\Factories\InventoryItemFactory;

echo "=== Factory Pattern Demo ===\n\n";

// Test Ring Item
echo "1. Testing Ring Item:\n";
$ringItem = InventoryItemFactory::create('RingItem', '18K Gold', 1500.00, [
    'stone_type' => 'Diamond',
    'ring_size' => 7
]);
echo "Description: " . $ringItem->getDescription() . "\n";
echo "Calculated Value: $" . $ringItem->calculateValue() . "\n\n";

// Test Necklace Item
echo "2. Testing Necklace Item:\n";
$necklaceItem = InventoryItemFactory::create('NecklaceItem', 'Pearl', 1200.00, [
    'necklace_length' => 45,
    'has_pendant' => true
]);
echo "Description: " . $necklaceItem->getDescription() . "\n";
echo "Calculated Value: $" . $necklaceItem->calculateValue() . "\n\n";

// Test Earrings Item
echo "3. Testing Earrings Item:\n";
$earringsItem = InventoryItemFactory::create('EarringsItem', '14K Gold', 600.00, [
    'earring_style' => 'Stud',
    'is_pair' => true
]);
echo "Description: " . $earringsItem->getDescription() . "\n";
echo "Calculated Value: $" . $earringsItem->calculateValue() . "\n\n";

// Test Bracelet Item
echo "4. Testing Bracelet Item:\n";
$braceletItem = InventoryItemFactory::create('BraceletItem', '18K Gold', 900.00, [
    'bracelet_clasp' => 'Magnetic',
    'adjustable' => true
]);
echo "Description: " . $braceletItem->getDescription() . "\n";
echo "Calculated Value: $" . $braceletItem->calculateValue() . "\n\n";

// Test Type Options
echo "5. Testing Type Options:\n";
$ringOptions = InventoryItemFactory::getTypeOptions('RingItem');
echo "Ring Options: " . json_encode($ringOptions, JSON_PRETTY_PRINT) . "\n\n";

// Test Validation
echo "6. Testing Validation:\n";
$errors = InventoryItemFactory::validateTypeData('RingItem', []);
echo "Validation Errors: " . json_encode($errors, JSON_PRETTY_PRINT) . "\n\n";

echo "=== Factory Pattern Demo Complete ===\n";


