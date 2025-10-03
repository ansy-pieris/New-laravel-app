# Install MongoDB Extension for XAMPP PHP 8.2
# Run this script as Administrator

Write-Host "Installing MongoDB Extension for XAMPP..." -ForegroundColor Green

# Download MongoDB extension DLL
$downloadUrl = "https://windows.php.net/downloads/pecl/releases/mongodb/1.19.1/php_mongodb-1.19.1-8.2-ts-vs16-x64.zip"
$zipFile = "php_mongodb.zip"
$extractPath = "mongodb_temp"

try {
    Write-Host "Downloading MongoDB extension..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri $downloadUrl -OutFile $zipFile
    
    Write-Host "Extracting extension..." -ForegroundColor Yellow
    Expand-Archive -Path $zipFile -DestinationPath $extractPath -Force
    
    # Copy DLL to PHP extensions directory
    $sourceDll = "$extractPath\php_mongodb.dll"
    $destPath = "C:\xampp\php\ext\php_mongodb.dll"
    
    if (Test-Path $sourceDll) {
        Copy-Item $sourceDll $destPath -Force
        Write-Host "Extension DLL copied to: $destPath" -ForegroundColor Green
        
        # Add extension to php.ini
        $phpIni = "C:\xampp\php\php.ini"
        $extensionLine = "extension=mongodb"
        
        $content = Get-Content $phpIni
        if ($content -notcontains $extensionLine) {
            Add-Content $phpIni "`n$extensionLine"
            Write-Host "Added 'extension=mongodb' to php.ini" -ForegroundColor Green
        }
        
        Write-Host "MongoDB extension installed successfully!" -ForegroundColor Green
        Write-Host "Please restart Apache and run: php -m | findstr mongodb" -ForegroundColor Yellow
    } else {
        Write-Host "Error: Extension DLL not found in downloaded package" -ForegroundColor Red
    }
} catch {
    Write-Host "Error downloading/installing extension: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    # Clean up
    if (Test-Path $zipFile) { Remove-Item $zipFile }
    if (Test-Path $extractPath) { Remove-Item $extractPath -Recurse -Force }
}