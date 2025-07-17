@echo off
echo Starting Reverb WebSocket server...
echo.

cd /d D:\Coding\phpAssignment\phpAssignment

echo Setting environment variables...
set REVERB_SERVER_PORT=8081
set REVERB_APP_KEY=reverb-key
set REVERB_APP_SECRET=reverb-secret
set REVERB_APP_ID=reverb-app-id
set REVERB_HOST=127.0.0.1
set REVERB_PORT=8081

echo Starting Reverb server on 127.0.0.1:8081...
php artisan reverb:start --port=8081 --host=127.0.0.1

pause 