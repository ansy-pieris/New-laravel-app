#!/bin/bash

# Create Sessions Table for Laravel
# Run this script on your EC2 instance

echo "🗄️ Creating Sessions Table for Laravel"
echo "======================================="

cd /var/www/html || exit 1

echo "1. Clearing configuration cache..."
sudo -u www-data php artisan config:clear

echo "2. Creating sessions table migration..."
sudo -u www-data php artisan session:table

echo "3. Running migrations to create sessions table..."
sudo -u www-data php artisan migrate --force

echo "4. Checking if sessions table was created..."
sudo -u www-data php artisan migrate:status | grep sessions

echo "5. Caching configuration..."
sudo -u www-data php artisan config:cache

echo ""
echo "✅ Sessions table setup complete!"
echo ""
echo "You can also run these commands individually:"
echo "sudo -u www-data php artisan session:table"
echo "sudo -u www-data php artisan migrate --force"