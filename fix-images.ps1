# ARES Apparel Store - Image Fix Utility (PowerShell)
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "   ARES Apparel Store - Image Fix Utility" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "This script will fix product image display issues" -ForegroundColor Yellow
Write-Host "by ensuring the storage symlink is properly configured." -ForegroundColor Yellow
Write-Host ""

# Change to script directory
Set-Location $PSScriptRoot

Write-Host "Checking if we're in the correct directory..." -ForegroundColor Gray
if (-not (Test-Path "artisan")) {
    Write-Host "ERROR: artisan file not found!" -ForegroundColor Red
    Write-Host "Make sure this script is in your Laravel project root directory." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "✅ Found Laravel project" -ForegroundColor Green
Write-Host ""

Write-Host "Fixing storage symlink..." -ForegroundColor Yellow
& php artisan storage:fix-symlink

Write-Host ""
Write-Host "Clearing application cache..." -ForegroundColor Yellow
& php artisan config:clear
& php artisan view:clear  
& php artisan cache:clear

Write-Host ""
Write-Host "===============================================" -ForegroundColor Green
Write-Host "              🎉 ALL DONE! 🎉" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Your product images should now display correctly!" -ForegroundColor Green
Write-Host "If you're still having issues, try:" -ForegroundColor Yellow
Write-Host "1. Restart your development server (php artisan serve)" -ForegroundColor White
Write-Host "2. Clear your browser cache (Ctrl+F5)" -ForegroundColor White
Write-Host "3. Check that images exist in storage/app/public/products/" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter to exit"