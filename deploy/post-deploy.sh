#!/bin/bash

# Post-deployment script for Laravel Apparel Store
# Run this after uploading the application files to EC2

set -e

echo "🔧 Running post-deployment configuration..."

# Navigate to application directory
cd /var/www/html

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Install NPM dependencies and build assets
echo "🏗️ Building frontend assets..."
npm install
npm run build

# Set proper permissions
echo "🔐 Setting proper permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database (optional)
echo "🌱 Seeding database..."
php artisan db:seed --force

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Create symbolic link for storage
echo "🔗 Creating storage symbolic link..."
php artisan storage:link

# Set final permissions
echo "🔧 Setting final permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Post-deployment configuration completed successfully!"
echo ""
echo "Your Laravel application should now be accessible at http://13.204.86.61"
echo ""
echo "Don't forget to:"
echo "1. Update your MongoDB Atlas connection string in .env"
echo "2. Configure your email settings in .env"
echo "3. Set up SSL certificate (recommended for production)"
echo "4. Configure firewall rules as needed"