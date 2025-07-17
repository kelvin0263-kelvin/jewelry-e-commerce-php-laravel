@echo off
echo Starting all services for the chat system...
echo.

echo 1. Starting Laravel development server...
start "Laravel Server" cmd /k "php artisan serve --port=8000"

echo 2. Starting Reverb WebSocket server...
start "Reverb Server" cmd /k "start-reverb-powershell.bat"

echo 3. Building assets with Vite...
start "Vite Build" cmd /k "npm run dev"

echo.
echo All services started!
echo.
echo Laravel Server: http://localhost:8000
echo Reverb WebSocket: ws://localhost:8081
echo.
echo You can now test the chat system at:
echo - Main app: http://localhost:8000
echo - Debug: http://localhost:8000/debug-chat
echo - Admin chat: http://localhost:8000/admin/chat
echo.
echo Press any key to exit...
pause 