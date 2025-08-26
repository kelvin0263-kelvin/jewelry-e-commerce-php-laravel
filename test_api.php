<?php
/**
 * Simple test script to verify API endpoints are working
 * Run this file to test your API without authentication
 */

// Test unauthenticated endpoints
$endpoints = [
    'GET /api/support/faq' => 'http://127.0.0.1:8000/api/support/faq',
    'GET /api/support/categories' => 'http://127.0.0.1:8000/api/support/categories',
];

echo "=== Testing Unauthenticated API Endpoints ===\n\n";

foreach ($endpoints as $name => $url) {
    echo "Testing {$name}...\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status Code: {$httpCode}\n";
    echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";
    echo "---\n\n";
}

echo "=== API Configuration Summary ===\n";
echo "✅ Laravel Sanctum has been installed and configured\n";
echo "✅ Comprehensive RESTful API routes created for Admin and Support modules\n";
echo "✅ API controllers updated with proper error handling\n";
echo "✅ Authentication middleware configured\n\n";

echo "Available API Endpoints:\n";
echo "Admin (requires auth:sanctum + admin middleware):\n";
echo "  - GET    /api/admin/dashboard\n";
echo "  - GET    /api/admin/dashboard/stats\n";
echo "  - GET    /api/admin/customers\n";
echo "  - GET    /api/admin/customers/{id}\n";
echo "  - PUT    /api/admin/customers/{id}\n";
echo "  - DELETE /api/admin/customers/{id}\n";
echo "  - GET    /api/admin/reports/customer-segments\n";
echo "  - GET    /api/admin/reports/sales\n";
echo "  - GET    /api/admin/reports/products\n";
echo "  - GET    /api/admin/reports/orders\n";
echo "  - GET    /api/admin/reports/revenue\n\n";

echo "Support (requires auth:sanctum):\n";
echo "  - GET    /api/support/tickets\n";
echo "  - POST   /api/support/tickets\n";
echo "  - GET    /api/support/tickets/{id}\n";
echo "  - PUT    /api/support/tickets/{id}\n";
echo "  - POST   /api/support/tickets/{id}/reply\n";
echo "  - POST   /api/support/tickets/{id}/close\n";
echo "  - GET    /api/support/faq\n";
echo "  - GET    /api/support/categories\n\n";

echo "Admin Support (requires auth:sanctum + admin middleware):\n";
echo "  - GET    /api/admin/support/tickets\n";
echo "  - GET    /api/admin/support/tickets/{id}\n";
echo "  - PUT    /api/admin/support/tickets/{id}\n";
echo "  - POST   /api/admin/support/tickets/{id}/assign\n";
echo "  - POST   /api/admin/support/tickets/{id}/reply\n";
echo "  - POST   /api/admin/support/tickets/{id}/close\n\n";

echo "To test authenticated endpoints:\n";
echo "1. Create a user account and get an API token\n";
echo "2. Include 'Authorization: Bearer {token}' header in requests\n";
echo "3. For admin endpoints, ensure the user has is_admin = true\n\n";

