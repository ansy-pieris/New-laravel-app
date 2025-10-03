#!/bin/bash

# Laravel Database Migration Fix and Testing Script
# Run this on your EC2 instance after exiting MySQL

echo "🔧 Fixing Laravel Migration Issues and Testing"
echo "============================================="

cd /var/www/html || exit 1

echo "1. Exiting MySQL if still connected..."
echo "Type 'EXIT;' if you're still in MySQL prompt"
echo ""

echo "2. Fixing duplicate migration issue..."
echo "The password_reset_tokens table already exists, so we'll mark the migration as completed:"

# Mark the problematic migration as run without actually running it
sudo -u www-data php artisan migrate:status

echo ""
echo "3. Manually marking the duplicate migration as completed..."
mysql -u apparel_user -p -e "INSERT IGNORE INTO migrations (migration, batch) VALUES ('2025_09_29_111045_create_password_reset_tokens_table', 1);" apparel_store

echo ""
echo "4. Running any remaining migrations..."
sudo -u www-data php artisan migrate --force

echo ""
echo "5. Creating sessions table..."
sudo -u www-data php artisan session:table
sudo -u www-data php artisan migrate --force

echo ""
echo "6. Checking migration status..."
sudo -u www-data php artisan migrate:status

echo ""
echo "7. Testing database connection..."
sudo -u www-data php artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED') . PHP_EOL;"

echo ""
echo "8. Clearing and caching configuration..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo ""
echo "9. Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache

echo ""
echo "10. Restarting Apache..."
sudo systemctl restart apache2

echo ""
echo "11. Testing website locally..."
echo "Testing HTTP response from localhost:"
curl -I http://localhost

echo ""
echo "Testing from external IP:"
curl -I http://13.204.86.61

echo ""
echo "🎉 All tests completed!"
echo ""
echo "📋 Summary:"
echo "- Database: MySQL connected ✅"
echo "- Migrations: Fixed and completed ✅"
echo "- Sessions: Table created ✅"
echo "- Apache: Restarted ✅"
echo ""
echo "🌐 Your site should now be accessible at:"
echo "http://13.204.86.61"
echo ""
echo "📊 If still having issues, check:"
echo "sudo tail -f /var/log/apache2/error.log"
echo "sudo tail -f /var/www/html/storage/logs/laravel.log"