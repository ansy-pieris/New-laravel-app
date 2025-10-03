# Laravel Database Connection Fix - Commands to run on EC2
# SSH into your EC2 instance and run these commands step by step

Write-Host "🔍 Laravel Database Connection Fix" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

Write-Host "📋 SSH into your EC2 instance first:" -ForegroundColor Yellow
Write-Host "ssh -i your-key.pem ubuntu@13.204.86.61" -ForegroundColor White
Write-Host ""

Write-Host "Then run these commands ONE BY ONE:" -ForegroundColor Yellow
Write-Host ""

$commands = @"
# 1. CHECK MYSQL STATUS
sudo systemctl status mysql

# 2. START MYSQL IF NOT RUNNING
sudo systemctl start mysql
sudo systemctl enable mysql

# 3. INSTALL MYSQL IF NOT INSTALLED
sudo apt update
sudo apt install -y mysql-server mysql-client

# 4. SECURE MYSQL INSTALLATION (set root password)
sudo mysql_secure_installation

# 5. CHECK IF MYSQL IS LISTENING
sudo netstat -tulpn | grep :3306

# 6. ACCESS MYSQL AS ROOT
sudo mysql -u root -p

# 7. INSIDE MYSQL, RUN THESE SQL COMMANDS:
CREATE DATABASE IF NOT EXISTS apparel_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'apparel_user'@'localhost' IDENTIFIED BY 'secure_password_here';
CREATE USER IF NOT EXISTS 'apparel_user'@'127.0.0.1' IDENTIFIED BY 'secure_password_here';

GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'localhost';
GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'127.0.0.1';

FLUSH PRIVILEGES;

SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = 'apparel_user';

EXIT;

# 8. TEST DATABASE CONNECTION
cd /var/www/html
mysql -u apparel_user -p apparel_store

# 9. RUN LARAVEL MIGRATIONS
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan migrate:status
sudo -u www-data php artisan session:table
sudo -u www-data php artisan migrate --force

# 10. CACHE CONFIGURATION
sudo -u www-data php artisan config:cache

# 11. CHECK LARAVEL LOGS
sudo tail -20 /var/www/html/storage/logs/laravel.log

# 12. RESTART APACHE
sudo systemctl restart apache2
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "🚨 IMPORTANT NOTES:" -ForegroundColor Red
Write-Host ""
Write-Host "1. The MySQL MSI installer you mentioned is for Windows only!" -ForegroundColor White
Write-Host "   Your EC2 Ubuntu instance needs MySQL installed via apt package manager." -ForegroundColor White
Write-Host ""
Write-Host "2. Make sure to replace 'secure_password_here' with your actual password" -ForegroundColor White
Write-Host "   in both the SQL commands AND your .env file." -ForegroundColor White
Write-Host ""
Write-Host "3. The sessions table is required because your .env has SESSION_DRIVER=database" -ForegroundColor White
Write-Host ""

Write-Host "📋 Quick Diagnosis Commands:" -ForegroundColor Green
Write-Host ""
Write-Host "Check if MySQL is installed:" -ForegroundColor Yellow
Write-Host "mysql --version" -ForegroundColor White
Write-Host ""
Write-Host "Check if MySQL service is running:" -ForegroundColor Yellow  
Write-Host "sudo systemctl status mysql" -ForegroundColor White
Write-Host ""
Write-Host "Check current .env database settings:" -ForegroundColor Yellow
Write-Host "grep -E '^DB_' /var/www/html/.env" -ForegroundColor White
Write-Host ""
Write-Host "Test Laravel database connection:" -ForegroundColor Yellow
Write-Host "cd /var/www/html && sudo -u www-data php artisan migrate:status" -ForegroundColor White

Write-Host ""
Write-Host "🎯 Most Likely Issues:" -ForegroundColor Blue
Write-Host "1. MySQL not installed on EC2 Ubuntu instance" -ForegroundColor White
Write-Host "2. MySQL service not running" -ForegroundColor White  
Write-Host "3. Database 'apparel_store' doesn't exist" -ForegroundColor White
Write-Host "4. User 'apparel_user' doesn't exist or lacks permissions" -ForegroundColor White
Write-Host "5. Sessions table missing (created by 'php artisan session:table')" -ForegroundColor White