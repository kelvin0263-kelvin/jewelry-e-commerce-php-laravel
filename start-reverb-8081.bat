@echo off
echo Starting Reverb server on port 8081...
echo.

REM Set environment variable for Reverb port
set REVERB_SERVER_PORT=8081

REM Start Reverb server
php artisan reverb:start --port=8081 --host=0.0.0.0

echo.
echo Reverb server stopped.
pause 