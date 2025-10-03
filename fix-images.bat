@echo off
echo ===============================================
echo   ARES Apparel Store - Image Fix Utility
echo ===============================================
echo.
echo This script will fix product image display issues
echo by ensuring the storage symlink is properly configured.
echo.

cd /d "%~dp0"

echo Checking if we're in the correct directory...
if not exist "artisan" (
    echo ERROR: artisan file not found!
    echo Make sure this script is in your Laravel project root directory.
    pause
    exit /b 1
)

echo ✅ Found Laravel project
echo.

echo Fixing storage symlink...
php artisan storage:fix-symlink

echo.
echo Clearing application cache...
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo.
echo ===============================================
echo              🎉 ALL DONE! 🎉
echo ===============================================
echo.
echo Your product images should now display correctly!
echo If you're still having issues, try:
echo 1. Restart your development server (php artisan serve)
echo 2. Clear your browser cache (Ctrl+F5)
echo 3. Check that images exist in storage/app/public/products/
echo.
pause