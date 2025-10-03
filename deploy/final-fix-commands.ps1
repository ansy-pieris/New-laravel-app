# Laravel Final Fix Commands - Run these on your EC2 instance
# You're currently in MySQL prompt, so first exit from it

Write-Host "🚀 Laravel Final Fix Commands" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green
Write-Host ""

Write-Host "📋 STEP 1: Exit from MySQL prompt" -ForegroundColor Yellow
Write-Host "You're currently in MySQL. Type this to exit:" -ForegroundColor White
Write-Host "EXIT;" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 STEP 2: Fix the migration conflict" -ForegroundColor Yellow
Write-Host "Run these commands after exiting MySQL:" -ForegroundColor White
Write-Host ""

$commands = @"
# Fix the duplicate migration issue
mysql -u apparel_user -p -D apparel_store -e "INSERT IGNORE INTO migrations (migration, batch) VALUES ('2025_09_29_111045_create_password_reset_tokens_table', 1);"

# Check migration status
sudo -u www-data php artisan migrate:status

# Run any remaining migrations
sudo -u www-data php artisan migrate --force

# Create sessions table
sudo -u www-data php artisan session:table
sudo -u www-data php artisan migrate --force

# Update to latest .env with database sessions
sudo git pull origin main

# Clear and cache configuration
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache

# Test database connection
sudo -u www-data php artisan tinker --execute="echo 'DB Connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED');"

# Restart Apache
sudo systemctl restart apache2

# Test the website
curl -I http://localhost
curl -I http://13.204.86.61
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "📊 Expected Results:" -ForegroundColor Green
Write-Host "✅ MySQL database connected" -ForegroundColor White
Write-Host "✅ All migrations completed" -ForegroundColor White
Write-Host "✅ Sessions table created" -ForegroundColor White
Write-Host "✅ Website responds with HTTP 200" -ForegroundColor White

Write-Host ""
Write-Host "🌐 Test URLs:" -ForegroundColor Blue
Write-Host "Local: curl -I http://localhost" -ForegroundColor White
Write-Host "External: http://13.204.86.61" -ForegroundColor White

Write-Host ""
Write-Host "🔍 If still having issues:" -ForegroundColor Red
Write-Host "sudo tail -20 /var/log/apache2/error.log" -ForegroundColor White
Write-Host "sudo tail -20 /var/www/html/storage/logs/laravel.log" -ForegroundColor White

Write-Host ""
Write-Host "🎉 You're almost done! The database is working, just need to fix the migration conflict and test!" -ForegroundColor Green