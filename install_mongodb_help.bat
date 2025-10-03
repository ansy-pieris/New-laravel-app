@echo off
echo MongoDB PHP Extension Installation Helper
echo =========================================
echo.
echo Your PHP Details:
echo - Version: 8.2.12
echo - Architecture: x64 (64-bit)
echo - Thread Safety: ZTS (Thread Safe)
echo - Extensions Directory: C:\xampp\php\ext
echo.
echo You need to download:
echo php_mongodb-1.18.0-8.2-ts-vs16-x64.zip
echo.
echo From: https://pecl.php.net/package/mongodb
echo.
echo Installation Steps:
echo 1. Download the ZIP file
echo 2. Extract php_mongodb.dll
echo 3. Copy php_mongodb.dll to C:\xampp\php\ext\
echo 4. Restart XAMPP Apache
echo.
echo After installation, run: php -m | findstr mongodb
echo.
pause