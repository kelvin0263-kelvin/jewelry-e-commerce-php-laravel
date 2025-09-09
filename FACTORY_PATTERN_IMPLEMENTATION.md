# Factory Design Pattern Implementation for Jewelry E-commerce

## Overview

This implementation demonstrates how the **Factory Design Pattern** is used to handle different variations of jewelry items in the inventory system. The pattern provides a clean, extensible way to create different types of jewelry items with their specific business logic and calculations.

## Database Structure

### Inventories Table
The `inventories` table contains both general and type-specific attributes:

```sql
CREATE TABLE inventories (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    type ENUM('RingItem', 'NecklaceItem', 'EarringsItem', 'BraceletItem'),
    description TEXT,
    price DECIMAL(10,2),
    quantity INTEGER,
    min_stock_level INTEGER,
    status ENUM('draft', 'published'),
    
    -- Type-specific attributes
    stone_type VARCHAR(50),        -- RingItem
    ring_size INTEGER,             -- RingItem
    necklace_length INTEGER,       -- NecklaceItem
    has_pendant BOOLEAN,           -- NecklaceItem
    earring_style VARCHAR(50),     -- EarringsItem
    is_pair BOOLEAN,               -- EarringsItem
    bracelet_clasp VARCHAR(50),    -- BraceletItem
    adjustable BOOLEAN,            -- BraceletItem
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Inventory Variations Table
The `inventory_variations` table stores variation-specific data with a `properties` JSON field:

```sql
CREATE TABLE inventory_variations (
    id BIGINT PRIMARY KEY,
    sku VARCHAR(255) UNIQUE,
    inventory_id BIGINT,
    color VARCHAR(50),
    size VARCHAR(50),
    material VARCHAR(100),
    price DECIMAL(10,2),
    stock INTEGER,
    image_path VARCHAR(255),
    properties JSON,               -- Factory-generated data
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Architecture

### 1. Abstract Base Class - InventoryItem

```php
abstract class InventoryItem
{
    protected string $type;
    protected string $material;
    protected float $price;

    abstract public function getDescription(): string;
    abstract public function calculateValue(): float;
}
```

### 2. Concrete Item Classes

Each jewelry type has its own class with specific properties and business logic:

#### RingItem
- **Properties**: `stoneType`, `size`
- **Business Logic**: Diamond premium (+$500), other stones (+$100)
- **Description**: "Gold Ring with Diamond stone, Size 7"

#### NecklaceItem
- **Properties**: `length`, `hasPendant`
- **Business Logic**: Length premium ($10/cm), pendant premium (+$200)
- **Description**: "Gold Necklace, 45cm, with pendant"

#### EarringsItem
- **Properties**: `style`, `isPair`
- **Business Logic**: Single earring = 60% of pair price
- **Description**: "Pair of Gold Stud Earrings"

#### BraceletItem
- **Properties**: `claspType`, `adjustable`
- **Business Logic**: Magnetic clasp premium (+$50)
- **Description**: "Gold Bracelet with Magnetic clasp, adjustable"

### 3. Factory Classes

#### InventoryItemFactory
Creates the appropriate item object based on type and inventory data:

```php
public static function create(string $type, string $material, float $price, array $inventoryData = []): InventoryItem
{
    return match ($type) {
        'RingItem' => new RingItem(
            $material, 
            $price, 
            $inventoryData['stone_type'] ?? 'Diamond', 
            (int)($inventoryData['ring_size'] ?? 7)
        ),
        'NecklaceItem' => new NecklaceItem(
            $material, 
            $price, 
            (int)($inventoryData['necklace_length'] ?? 45), 
            (bool)($inventoryData['has_pendant'] ?? false)
        ),
        // ... other types
    };
}
```

## Usage Examples

### 1. Creating a Ring with Type-Specific Attributes

```php
$ringData = [
    'name' => 'Diamond Ring Collection',
    'type' => 'RingItem',
    'description' => 'Beautiful diamond rings',
    'price' => 1500.00,
    // Type-specific attributes
    'stone_type' => 'Diamond',
    'ring_size' => 7,
    'variations' => [
        [
            'sku' => 'RING-001',
            'color' => 'Gold',
            'material' => '18K Gold',
            'price' => 1500.00,
            'stock' => 10,
        ]
    ]
];

$inventory = Inventory::create($ringData);

// Create variations using factory pattern
foreach ($ringData['variations'] as $variation) {
    $item = $inventory->createInventoryItem($variation);
    
    $inventory->variations()->create([
        'sku' => $variation['sku'],
        'color' => $variation['color'],
        'material' => $variation['material'],
        'price' => $item->calculateValue(), // Factory-calculated value
        'stock' => $variation['stock'],
        'properties' => [
            'description' => $item->getDescription(),
            'calculated_value' => $item->calculateValue(),
            'variation_data' => $variation,
            'factory_created_at' => now()->toISOString(),
        ],
    ]);
}
```

### 2. Creating a Necklace with Type-Specific Attributes

```php
$necklaceData = [
    'name' => 'Pearl Necklace Collection',
    'type' => 'NecklaceItem',
    'description' => 'Elegant pearl necklaces',
    'price' => 1200.00,
    // Type-specific attributes
    'necklace_length' => 45,
    'has_pendant' => true,
    'variations' => [
        [
            'sku' => 'NECK-001',
            'color' => 'White',
            'material' => 'Pearl',
            'price' => 1200.00,
            'stock' => 8,
        ]
    ]
];

$inventory = Inventory::create($necklaceData);
// ... create variations using factory pattern
```

### 3. Getting Enhanced Data

```php
$inventory = Inventory::with('variations')->find(1);

// Get factory-enhanced data
$calculatedValue = $inventory->getCalculatedValue();
$description = $inventory->getItemDescription();
$typeOptions = $inventory->getTypeOptions();

// Get variations with factory data
$enhancedVariations = $inventory->variations->map(function ($variation) {
    return [
        'sku' => $variation->sku,
        'price' => $variation->price,
        'factory_description' => $variation->properties['description'],
        'factory_calculated_value' => $variation->properties['calculated_value'],
    ];
});
```

## API Endpoints

### Create Inventory with Factory Pattern
```http
POST /api/inventory
Content-Type: application/json

{
    "name": "Diamond Ring Collection",
    "type": "RingItem",
    "description": "Beautiful diamond rings",
    "price": 1500.00,
    "stone_type": "Diamond",
    "ring_size": 7,
    "variations": [
        {
            "sku": "RING-001",
            "color": "Gold",
            "material": "18K Gold",
            "price": 1500.00,
            "stock": 10
        }
    ]
}
```

### Response with Factory-Enhanced Data
```json
{
    "id": 1,
    "name": "Diamond Ring Collection",
    "type": "RingItem",
    "stone_type": "Diamond",
    "ring_size": 7,
    "variations": [
        {
            "id": 1,
            "sku": "RING-001",
            "price": 2000.00,
            "properties": {
                "description": "18K Gold Ring with Diamond stone, Size 7",
                "calculated_value": 2000.00,
                "variation_data": {...},
                "factory_created_at": "2025-09-09T04:01:07.000000Z"
            }
        }
    ]
}
```

## Validation

The factory pattern includes type-specific validation:

```php
// Get validation rules for a type
$rules = InventoryItemFactory::getValidationRules('RingItem');
// Returns: ['stone_type' => 'required|string|max:50', 'ring_size' => 'required|integer|min:4|max:12']

// Validate type-specific data
$errors = InventoryItemFactory::validateTypeData('RingItem', $data);
// Returns: ['Field stone_type is required for RingItem']
```

## Type Options

Get available options for each type:

```php
$options = InventoryItemFactory::getTypeOptions('RingItem');
// Returns: ['stone_types' => [...], 'ring_sizes' => [...]]
```

## Benefits of This Implementation

### 1. **Type-Specific Attributes in Inventory**
- Each jewelry type has its specific attributes stored in the inventory record
- Type-specific business logic applied at the inventory level
- Easy to query and filter by type-specific attributes

### 2. **Factory Pattern for Variations**
- Each variation uses the factory pattern to calculate values
- Type-specific business logic applied to variations
- Enhanced data stored in the `properties` JSON field

### 3. **Enhanced Data Storage**
- `properties` field stores factory-generated data
- Calculated values, descriptions, and timestamps
- Type-specific data preserved for each variation

### 4. **Extensibility**
- Easy to add new jewelry types
- Just create a new class extending `InventoryItem`
- Add the new type to the factory's match statement
- Add type-specific fields to the inventory table

### 5. **Consistency**
- Uniform object creation across all jewelry types
- Consistent business logic application
- Standardized data structure

## Demo Controller

Use the `FactoryDemoController` to see the factory pattern in action:

```http
GET /api/factory-demo
GET /api/factory-demo/{inventoryId}/enhanced-data
GET /api/factory-demo/type-options/{type}
```

## Migration

Run the migrations to set up the database structure:

```bash
php artisan migrate
```

## Conclusion

This factory pattern implementation provides:

1. **Clean Architecture**: Type-specific attributes in inventory, factory pattern for variations
2. **Type Safety**: Each jewelry type has its own validation and business rules
3. **Enhanced Data**: Automatic calculation of values and generation of descriptions
4. **Extensibility**: Easy to add new jewelry types without modifying existing code
5. **Consistency**: Uniform object creation and business logic application

The pattern makes the codebase more maintainable, testable, and scalable for future jewelry types and business requirements.


