# Manual Startup Guide for Real-Time Chat

## Current Issue: WebSocket Connection Disconnected

The Reverb server is not running properly on port 8081. Let's fix this step by step.

## Step 1: Stop All Services

1. Close ALL command windows/terminals
2. Stop any running PHP processes
3. Make sure no Laravel or Reverb servers are running

## Step 2: Clear Laravel Cache

```bash
cd /d D:\Coding\phpAssignment\phpAssignment
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Step 3: Start Services Manually (One by One)

### Terminal 1: Laravel Server
```bash
cd /d D:\Coding\phpAssignment\phpAssignment
php artisan serve --port=8000
```
**Expected output:** `Laravel development server started on http://localhost:8000`

### Terminal 2: Reverb Server (IMPORTANT)
```bash
cd /d D:\Coding\phpAssignment\phpAssignment
set REVERB_SERVER_PORT=8081
php artisan reverb:start --port=8081 --host=0.0.0.0 --debug
```
**Expected output:** Something like:
```
Starting Reverb server on port 8081...
Reverb server started successfully
```

### Terminal 3: Asset Compilation
```bash
cd /d D:\Coding\phpAssignment\phpAssignment
npm run dev
```
**Expected output:** Vite development server starting

## Step 4: Test Each Service

### Test 1: Laravel Server
- Visit: `http://localhost:8000`
- Should show your Laravel application

### Test 2: Reverb Server
- Open: `test-websocket.html` in browser
- Should show "✅ Connected"

### Test 3: Real-Time Debug
- Visit: `http://localhost:8000/debug-realtime`
- All status cards should be green

## Step 5: Common Issues and Solutions

### Issue: "Command not found" or "artisan not found"
**Solution:** Make sure you're in the correct directory:
```bash
cd /d D:\Coding\phpAssignment\phpAssignment
```

### Issue: "Port already in use"
**Solution:** Find and kill the process using the port:
```bash
netstat -ano | findstr :8081
taskkill /PID <PID_NUMBER> /F
```

### Issue: Reverb server starts but WebSocket still disconnected
**Solution:** Check if Windows Firewall is blocking the connection:
1. Go to Windows Defender Firewall
2. Allow PHP through firewall
3. Or temporarily disable firewall for testing

### Issue: "npm run dev" fails
**Solution:** Install dependencies:
```bash
npm install
```

## Step 6: Verify Everything is Working

1. **Check ports are in use:**
   ```bash
   netstat -an | findstr :8000
   netstat -an | findstr :8081
   ```

2. **Test WebSocket connection:**
   - Open `test-websocket.html`
   - Should show "✅ Connected"

3. **Test real-time chat:**
   - Visit `/debug-realtime`
   - All status should be green
   - Send test messages

## Troubleshooting Commands

```bash
# Check if Reverb server is running
netstat -an | findstr :8081

# Check PHP processes
tasklist | findstr php

# Check Laravel configuration
php artisan config:show broadcasting.connections.reverb.options.port

# Test Reverb server manually
php artisan reverb:start --port=8081 --host=0.0.0.0 --debug

# Clear all Laravel cache
php artisan optimize:clear
```

## If Still Not Working

1. Check Windows Event Viewer for errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Try using a different port (8082) and update all configurations
4. Check if antivirus software is blocking the connection 