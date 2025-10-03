#!/bin/bash

# Laravel Apparel Store Deployment Script for Ubuntu 24.04 EC2
# Run this script on your EC2 instance as root or with sudo

set -e

echo "🚀 Starting Laravel Apparel Store Deployment..."

# Update system packages
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install required packages
echo "📋 Installing required packages..."
apt install -y apache2 mysql-server php8.3 php8.3-fpm php8.3-mysql php8.3-xml php8.3-gd php8.3-mbstring php8.3-zip php8.3-curl php8.3-bcmath php8.3-intl php8.3-redis composer nodejs npm git unzip

# Enable required Apache modules
echo "🔧 Enabling Apache modules..."
a2enmod rewrite headers ssl php8.3

# Install MongoDB PHP extension
echo "🍃 Installing MongoDB PHP extension..."
apt install -y php8.3-dev pkg-config libssl-dev
pecl install mongodb
echo "extension=mongodb.so" > /etc/php/8.3/apache2/conf.d/20-mongodb.ini
echo "extension=mongodb.so" > /etc/php/8.3/cli/conf.d/20-mongodb.ini

# Start and enable services
echo "🚀 Starting services..."
systemctl start apache2
systemctl enable apache2
systemctl start mysql
systemctl enable mysql

# Secure MySQL installation (you'll need to run this interactively)
echo "🔒 MySQL is installed. You'll need to run 'mysql_secure_installation' manually."

# Create MySQL database and user
echo "🗄️ Setting up MySQL database..."
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS apparel_store;
CREATE USER IF NOT EXISTS 'apparel_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# Set up application directory
echo "📁 Setting up application directory..."
rm -rf /var/www/html/*
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

echo "📋 Deployment script completed successfully!"
echo ""
echo "Next steps:"
echo "1. Upload your Laravel application files to /var/www/html/"
echo "2. Copy the .env.production file to /var/www/html/.env"
echo "3. Copy the Apache configuration file to /etc/apache2/sites-available/"
echo "4. Run the post-deployment script"
echo ""
echo "Commands to run after uploading files:"
echo "sudo cp /var/www/html/deploy/apache-apparel-store.conf /etc/apache2/sites-available/"
echo "sudo a2ensite apache-apparel-store.conf"
echo "sudo a2dissite 000-default.conf"
echo "sudo systemctl reload apache2"
echo "sudo bash /var/www/html/deploy/post-deploy.sh"