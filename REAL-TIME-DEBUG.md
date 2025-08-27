# Real-time Messaging Debug Guide

## Step-by-step debugging for customer not receiving admin messages:

### 1. Check Reverb Server is Running
```bash
php artisan reverb:start
```
Make sure you see "Reverb server started on 127.0.0.1:8081"

### 2. Check Browser Console (Customer Side)
When customer opens chat, you should see:
```
✅ Echo connected to Reverb server on port 8081
🎧 Setting up Echo message listening for conversation: [ID]
📡 Attempting to subscribe to channel: conversation.[ID]
✅ Successfully subscribed to channel: conversation.[ID]
```

### 3. Check Broadcasting Authentication
In browser console, check for:
```
❌ Echo authentication error: [error details]
```
If you see auth errors, the customer can't subscribe to private channels.

### 4. Test Message Broadcasting (Admin Side)
When admin sends a message, check browser console for:
```
BroadcastObserver: Message broadcast sent
```

### 5. Check Customer Receives Event
When admin sends message, customer console should show:
```
📨 [CUSTOMER] Received MessageSent event: {message: {...}}
👤 Current user ID: [customer_id]
📝 Message details: {messageUserId: [admin_id], ...}
✅ [CUSTOMER] Adding agent message to chat
```

### 6. Manual Test Commands

#### Customer Side (Browser Console):
```javascript
// Check if Echo is connected
console.log('Echo status:', window.Echo?.connector?.pusher?.connection?.state);

// Check current subscription
console.log('Current channel:', window.currentEchoChannel);

// Manually test subscription
window.Echo.private('conversation.1')
  .subscribed(() => console.log('✅ Manual subscription successful'))
  .error(error => console.log('❌ Manual subscription failed:', error));
```

#### Test Broadcasting (Laravel Tinker):
```php
php artisan tinker

// Test broadcast directly
$message = App\Modules\Support\Models\Message::with('user')->first();
broadcast(new App\Modules\Support\Events\MessageSent($message));

// Check if conversation channel exists
$conversation = App\Modules\Support\Models\Conversation::first();
broadcast(new App\Modules\Support\Events\MessageSent($message))->toOthers();
```

### Common Issues:

1. **Reverb server not running** → Start with `php artisan reverb:start`
2. **Authentication issues** → Check `/broadcasting/auth` route works
3. **Channel name mismatch** → Should be `conversation.{id}` 
4. **User filtering** → Admin messages filtered out by customer listener
5. **Multiple subscriptions** → Old subscriptions not properly cleaned up

### Expected Flow:
1. Admin sends message → ChatController::store() 
2. Creates Message model → emitMessageSent()
3. ChatEventManager → notifies BroadcastObserver
4. BroadcastObserver → broadcast(MessageSent)
5. Reverb → pushes to conversation.{id} channel
6. Customer Echo listener → receives event → adds to chat