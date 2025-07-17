# Chat System Troubleshooting Guide

## Quick Start

1. **Start all services:**
   ```bash
   # Run this batch file to start everything
   start-all.bat
   ```

2. **Or start manually:**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve --port=8000

   # Terminal 2: Reverb WebSocket server
   php artisan reverb:start --port=8081

   # Terminal 3: Vite asset compilation
   npm run dev
   ```

## Testing Steps

### 1. Test Database Connection
Visit: `http://localhost:8000/debug-chat`

Expected response:
```json
{
  "status": "success",
  "data": {
    "conversations": 1,
    "messages": 7,
    "users": 1,
    "database_working": true
  }
}
```

### 2. Test Chat Controller
Visit: `http://localhost:8000/debug-chat-controller`

Expected response:
```json
{
  "status": "success",
  "conversation_id": 1,
  "messages": [...],
  "controller_working": true
}
```

### 3. Test Authentication
- Login to the application
- Visit: `http://localhost:8000/test-chat`
- Try the chat functionality

### 4. Test Admin Chat
- Login as admin user
- Visit: `http://localhost:8000/admin/chat`
- Try sending messages

## Common Issues and Solutions

### Issue 1: "Chat not functioning"
**Symptoms:** Chat widget not loading, messages not sending
**Solutions:**
1. Check if all servers are running (Laravel, Reverb, Vite)
2. Check browser console for JavaScript errors
3. Verify CSRF token is present in page header
4. Check if user is authenticated

### Issue 2: "Real-time messages not working"
**Symptoms:** Messages send but don't appear in real-time
**Solutions:**
1. Verify Reverb server is running on port 8081
2. Check browser console for WebSocket connection errors
3. Verify Echo configuration in `resources/js/echo.js`
4. Check broadcasting channel authorization in `routes/channels.php`

### Issue 3: "404 errors on chat routes"
**Symptoms:** API endpoints returning 404
**Solutions:**
1. Run `php artisan route:list | findstr chat` to verify routes
2. Check if routes are properly authenticated
3. Verify ChatController exists and methods are correct

### Issue 4: "403 Forbidden errors"
**Symptoms:** API endpoints returning 403
**Solutions:**
1. Check if user is authenticated
2. Verify CSRF token is being sent with requests
3. Check middleware configuration

### Issue 5: "Database errors"
**Symptoms:** Errors about missing tables or columns
**Solutions:**
1. Run `php artisan migrate` to create tables
2. Check if conversations and messages tables exist
3. Verify model relationships are correct

## Debug Commands

```bash
# Check migration status
php artisan migrate:status

# Check routes
php artisan route:list | findstr chat

# Check database tables
php artisan tinker --execute="print_r(DB::select('SHOW TABLES'));"

# Check conversations and messages count
php artisan tinker --execute="echo 'Conversations: ' . App\Models\Conversation::count() . ', Messages: ' . App\Models\Message::count();"

# Test broadcasting
php artisan tinker --execute="broadcast(new App\Events\MessageSent(App\Models\Message::first()));"
```

## File Locations

- **Chat Widget:** `resources/views/components/chat-widget.blade.php`
- **Admin Chat:** `resources/views/admin/chat/index.blade.php`
- **Chat Controller:** `app/Http/Controllers/Api/ChatController.php`
- **Routes:** `routes/web.php` and `routes/api.php`
- **Echo Config:** `resources/js/echo.js`
- **Broadcasting Config:** `config/broadcasting.php`
- **Channel Auth:** `routes/channels.php`

## Browser Console Debugging

Open browser console (F12) and look for:
- WebSocket connection messages
- JavaScript errors
- Network request failures
- Echo connection status

## Contact

If issues persist, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console errors
3. Network tab in browser developer tools
4. Server terminal output for errors 