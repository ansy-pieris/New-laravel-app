#!/bin/bash

# Laravel Apparel Store - Complete EC2 Deployment Script
# Run this script on your EC2 instance to ensure proper configuration

echo "🚀 Starting Laravel Apparel Store deployment configuration..."

# 1. Update system packages
echo "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# 2. Enable required Apache modules
echo "🔧 Enabling Apache modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo a2enmod ssl

# 3. Copy the comprehensive Apache configuration
echo "⚙️ Configuring Apache virtual host..."
sudo cp /var/www/html/deploy/apache-apparel-store-complete.conf /etc/apache2/sites-available/apparel-store.conf
sudo a2dissite 000-default
sudo a2ensite apparel-store

# 4. Set proper permissions
echo "🔐 Setting proper file permissions..."
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache

# 5. Install Composer dependencies (if not already done)
echo "📚 Installing Composer dependencies..."
cd /var/www/html || exit 1
sudo -u www-data composer install --no-dev --optimize-autoloader

# 6. Generate application key (if not already done)
echo "🔑 Ensuring application key is set..."
sudo -u www-data php artisan key:generate --force

# 7. Clear and cache configurations
echo "🧹 Clearing and caching configurations..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 8. Run database migrations
echo "🗄️ Running database migrations..."
sudo -u www-data php artisan migrate --force

# 9. Check Apache configuration
echo "✅ Testing Apache configuration..."
sudo apache2ctl configtest

# 10. Restart Apache
echo "🔄 Restarting Apache..."
sudo systemctl restart apache2

# 11. Enable Apache to start on boot
echo "🚀 Enabling Apache to start on boot..."
sudo systemctl enable apache2

# 12. Check services status
echo "📊 Checking service status..."
sudo systemctl status apache2 --no-pager -l
sudo systemctl status mysql --no-pager -l

# 13. Check if port 80 is open
echo "🌐 Checking network configuration..."
sudo netstat -tulpn | grep :80
sudo ss -tulpn | grep :80

# 14. Check firewall settings
echo "🔥 Checking firewall settings..."
sudo ufw status

echo ""
echo "🎉 Deployment configuration complete!"
echo ""
echo "📋 Next steps to troubleshoot ERR_CONNECTION_REFUSED:"
echo ""
echo "1. Check if Apache is running:"
echo "   sudo systemctl status apache2"
echo ""
echo "2. Check if port 80 is open:"
echo "   sudo netstat -tulpn | grep :80"
echo ""
echo "3. Check Security Group settings in AWS EC2 Console:"
echo "   - Ensure inbound rule allows HTTP (port 80) from 0.0.0.0/0"
echo "   - Ensure inbound rule allows HTTPS (port 443) from 0.0.0.0/0"
echo ""
echo "4. Check Ubuntu firewall (UFW):"
echo "   sudo ufw status"
echo "   sudo ufw allow 80/tcp"
echo "   sudo ufw allow 443/tcp"
echo ""
echo "5. Test Apache default page:"
echo "   curl -I http://localhost"
echo "   curl -I http://13.204.86.61"
echo ""
echo "6. Check Apache error logs:"
echo "   sudo tail -f /var/log/apache2/error.log"
echo "   sudo tail -f /var/log/apache2/apparel-store-error.log"
echo ""
echo "🔗 Your site should be accessible at: http://13.204.86.61"