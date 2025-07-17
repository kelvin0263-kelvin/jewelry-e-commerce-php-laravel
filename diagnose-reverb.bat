@echo off
echo ==========================================
echo         REVERB SERVER DIAGNOSTICS
echo ==========================================
echo.

echo 1. Checking if port 8081 is in use...
netstat -an | findstr :8081
if %errorlevel% neq 0 (
    echo   ❌ Port 8081 is NOT in use - Reverb server is not running!
) else (
    echo   ✅ Port 8081 is in use
)
echo.

echo 2. Checking for running PHP processes...
tasklist | findstr php.exe
if %errorlevel% neq 0 (
    echo   ❌ No PHP processes found
) else (
    echo   ✅ PHP processes found
)
echo.

echo 3. Testing if we can start Reverb server...
echo   Starting Reverb server test (will stop after 5 seconds)...
timeout /t 2 /nobreak > nul
start /min cmd /c "php artisan reverb:start --port=8081 --host=0.0.0.0"
timeout /t 5 /nobreak > nul
taskkill /f /im php.exe > nul 2>&1
echo   Test completed.
echo.

echo 4. Checking Laravel configuration...
php artisan config:show broadcasting.default
php artisan config:show broadcasting.connections.reverb.options.port
echo.

echo 5. Testing WebSocket connection...
echo   You can test WebSocket by opening: test-websocket.html
echo.

echo ==========================================
echo         DIAGNOSTIC COMPLETE
echo ==========================================
echo.
echo Next steps:
echo 1. If port 8081 is not in use, start Reverb server manually
echo 2. If PHP processes are not found, check Laravel installation
echo 3. If configuration is wrong, run: php artisan config:clear
echo.
pause 