<?php
// Simple test script to check if the chat system is working
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test script to verify chat components
echo "=== CHAT SYSTEM TEST ===\n\n";

echo "1. Checking if ChatController exists...\n";
if (class_exists('App\Modules\Support\Controllers\ChatController')) {
    echo "✅ ChatController found\n";
} else {
    echo "❌ ChatController not found\n";
}

echo "\n2. Checking if ChatQueueService exists...\n";
if (class_exists('App\Modules\Support\Services\ChatQueueService')) {
    echo "✅ ChatQueueService found\n";
} else {
    echo "❌ ChatQueueService not found\n";
}

echo "\n3. Checking if MessageSent event exists...\n";
if (class_exists('App\Modules\Support\Events\MessageSent')) {
    echo "✅ MessageSent event found\n";
} else {
    echo "❌ MessageSent event not found\n";
}

echo "\n4. Checking if Conversation model exists...\n";
if (class_exists('App\Modules\Support\Models\Conversation')) {
    echo "✅ Conversation model found\n";
} else {
    echo "❌ Conversation model not found\n";
}

echo "\n5. Checking if Message model exists...\n";
if (class_exists('App\Modules\Support\Models\Message')) {
    echo "✅ Message model found\n";
} else {
    echo "❌ Message model not found\n";
}

echo "\n6. Checking if ChatQueue model exists...\n";
if (class_exists('App\Modules\Support\Models\ChatQueue')) {
    echo "✅ ChatQueue model found\n";
} else {
    echo "❌ ChatQueue model not found\n";
}

echo "\n=== CONFIGURATION CHECK ===\n";

// Check if we can access .env
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ .env file found\n";
    
    // Read env for broadcast config
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (strpos($envContent, 'BROADCAST_CONNECTION') !== false) {
        echo "✅ BROADCAST_CONNECTION configured\n";
    } else {
        echo "⚠️  BROADCAST_CONNECTION not found in .env\n";
        echo "Add: BROADCAST_CONNECTION=reverb\n";
    }
    
    if (strpos($envContent, 'REVERB_APP_KEY') !== false) {
        echo "✅ Reverb configuration found\n";
    } else {
        echo "⚠️  Reverb configuration incomplete\n";
        echo "Add:\n";
        echo "REVERB_APP_KEY=reverb-key\n";
        echo "REVERB_APP_SECRET=reverb-secret\n";
        echo "REVERB_APP_ID=reverb-app-id\n";
        echo "REVERB_HOST=localhost\n";
        echo "REVERB_PORT=8081\n";
        echo "REVERB_SCHEME=http\n";
    }
} else {
    echo "❌ .env file not found\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Make sure Reverb server is running:\n";
echo "   php artisan reverb:start\n\n";
echo "2. Make sure assets are compiled:\n";
echo "   npm run dev\n\n";
echo "3. Check if these routes work:\n";
echo "   POST /chat/start\n";
echo "   POST /chat/messages\n";
echo "   GET /chat/conversations/{id}/messages\n\n";
echo "4. Test the chat widget at your main site\n\n";