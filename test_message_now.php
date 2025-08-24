<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Modules\Support\Models\Message;
use App\Modules\Support\Events\MessageSent;

echo "🚨 CREATING TEST MESSAGE NOW 🚨\n";

$message = Message::create([
    'conversation_id' => 70,
    'user_id' => 2,
    'body' => '⚡ APPROACH TEST: ' . date('H:i:s')
]);

$message->load('user');

echo "Message: {$message->body}\n";
echo "Broadcasting to channel: chat.{$message->conversation_id}\n";

broadcast(new MessageSent($message));

echo "✅ BROADCAST SENT! Check your browser console NOW!\n";
