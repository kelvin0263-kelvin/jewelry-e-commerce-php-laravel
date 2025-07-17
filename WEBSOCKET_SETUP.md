# WebSocket Setup Guide (PowerShell Compatible)

## Step 1: Manual Startup (Recommended)

### Terminal 1: Laravel Server
```powershell
cd D:\Coding\phpAssignment\phpAssignment
php artisan serve --port=8000
```

### Terminal 2: Reverb WebSocket Server
```powershell
cd D:\Coding\phpAssignment\phpAssignment
$env:REVERB_SERVER_PORT = "8081"
$env:REVERB_APP_KEY = "reverb-key"
$env:REVERB_APP_SECRET = "reverb-secret"
$env:REVERB_APP_ID = "reverb-app-id"
php artisan reverb:start --port=8081 --host=0.0.0.0 --debug
```

### Terminal 3: Vite Assets
```powershell
cd D:\Coding\phpAssignment\phpAssignment
npm run dev
```

## Step 2: Alternative Startup Methods

### Option A: Use Batch Files
1. Double-click `start-reverb-powershell.bat`
2. Wait for "Reverb server started" message
3. Open another terminal for Laravel server

### Option B: Use PowerShell Script
```powershell
# Right-click and "Run with PowerShell"
.\test-reverb.ps1
```

## Step 3: Test WebSocket Connection

1. **Basic Test**: Open `http://localhost:8000/websocket-test.html`
2. **Laravel Test**: Open `http://localhost:8000/debug-realtime`

## Step 4: Expected Results

### If Working:
- WebSocket test shows: "✅ Connected to WebSocket server"
- Debug page shows all green status cards
- Port 8081 shows in `netstat -an | findstr :8081`

### If Not Working:
- Check if Reverb server is actually running
- Look for error messages in the Reverb terminal
- Check Windows Firewall settings

## Common Commands (PowerShell Friendly)

```powershell
# Check if port is in use
netstat -an | findstr :8081

# Check running PHP processes
tasklist | findstr php

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear

# Check Reverb commands
php artisan list | findstr reverb

# Kill PHP processes if needed
taskkill /f /im php.exe
```

## Troubleshooting

### Issue: "Command not found"
**Solution**: Make sure you're in the correct directory:
```powershell
cd D:\Coding\phpAssignment\phpAssignment
```

### Issue: "Port already in use"
**Solution**: 
```powershell
netstat -ano | findstr :8081
# Find the PID and kill it:
taskkill /PID <PID_NUMBER> /F
```

### Issue: Reverb server won't start
**Solution**: 
1. Check if Reverb is installed: `php artisan list | findstr reverb`
2. Try installing: `php artisan reverb:install`
3. Check Laravel logs: `storage/logs/laravel.log`

## Next Steps

Once all three services are running:
1. Test WebSocket connection
2. Test real-time chat functionality
3. Check browser console for any errors 