# PowerShell script to test and start Reverb server
Write-Host "=== Reverb WebSocket Server Test ===" -ForegroundColor Green
Write-Host ""

# Navigate to project directory
Set-Location "D:\Coding\phpAssignment\phpAssignment"

# Check if we're in the right directory
if (Test-Path "artisan") {
    Write-Host "✓ Found Laravel project" -ForegroundColor Green
} else {
    Write-Host "✗ Laravel project not found" -ForegroundColor Red
    exit 1
}

# Set environment variables
$env:REVERB_SERVER_PORT = "8081"
$env:REVERB_APP_KEY = "reverb-key"
$env:REVERB_APP_SECRET = "reverb-secret"
$env:REVERB_APP_ID = "reverb-app-id"

Write-Host "Environment variables set:" -ForegroundColor Yellow
Write-Host "  REVERB_SERVER_PORT: $env:REVERB_SERVER_PORT"
Write-Host "  REVERB_APP_KEY: $env:REVERB_APP_KEY"
Write-Host ""

# Check if port 8081 is already in use
$portCheck = netstat -an | Select-String ":8081"
if ($portCheck) {
    Write-Host "⚠ Port 8081 is already in use:" -ForegroundColor Yellow
    Write-Host $portCheck
    Write-Host ""
}

# Test if artisan reverb commands are available
Write-Host "Testing Reverb commands..." -ForegroundColor Yellow
try {
    $reverbCommands = php artisan list | Select-String "reverb"
    if ($reverbCommands) {
        Write-Host "✓ Reverb commands available:" -ForegroundColor Green
        $reverbCommands | ForEach-Object { Write-Host "  $_" }
    } else {
        Write-Host "✗ No Reverb commands found" -ForegroundColor Red
    }
} catch {
    Write-Host "✗ Error checking Reverb commands: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "Starting Reverb server..." -ForegroundColor Green
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start Reverb server
try {
    php artisan reverb:start --port=8081 --host=0.0.0.0 --debug
} catch {
    Write-Host "✗ Error starting Reverb server: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "Reverb server stopped." -ForegroundColor Yellow 