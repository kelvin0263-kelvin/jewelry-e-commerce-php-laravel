@echo off
echo Starting Reverb WebSocket server on port 8081...
echo.

REM Navigate to project directory
cd /d D:\Coding\phpAssignment\phpAssignment

REM Set environment variables
set REVERB_SERVER_PORT=8081
set REVERB_APP_KEY=reverb-key
set REVERB_APP_SECRET=reverb-secret
set REVERB_APP_ID=reverb-app-id

REM Start Reverb server
echo Starting Reverb server with debug output...
php artisan reverb:start --port=8081 --host=0.0.0.0 --debug

echo.
echo Reverb server stopped.
pause 