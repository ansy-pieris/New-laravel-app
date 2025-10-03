#!/bin/bash

# Laravel Apparel Store - Database Connection Troubleshooting Script
# Run this on your Ubuntu 24.04 EC2 instance

echo "🔍 Laravel Database Connection Troubleshooting"
echo "=============================================="
echo ""

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# 1. Check if MySQL is installed
echo "📋 Step 1: Checking MySQL Installation"
echo "-------------------------------------"

if command_exists mysql; then
    echo "✅ MySQL client is installed"
    mysql --version
else
    echo "❌ MySQL client not found. Installing MySQL..."
    sudo apt update
    sudo apt install -y mysql-server mysql-client
fi

# 2. Check MySQL service status
echo ""
echo "📋 Step 2: Checking MySQL Service Status"
echo "----------------------------------------"

if systemctl is-active --quiet mysql; then
    echo "✅ MySQL service is running"
    sudo systemctl status mysql --no-pager -l
else
    echo "❌ MySQL service is not running. Starting it..."
    sudo systemctl start mysql
    sudo systemctl enable mysql
    echo "✅ MySQL service started and enabled"
fi

# 3. Check if MySQL is listening on port 3306
echo ""
echo "📋 Step 3: Checking MySQL Port"
echo "------------------------------"

if sudo netstat -tulpn | grep :3306 > /dev/null; then
    echo "✅ MySQL is listening on port 3306"
    sudo netstat -tulpn | grep :3306
else
    echo "❌ MySQL is not listening on port 3306"
    echo "Checking MySQL configuration..."
    sudo cat /etc/mysql/mysql.conf.d/mysqld.cnf | grep bind-address
fi

# 4. Test MySQL root connection
echo ""
echo "📋 Step 4: Testing MySQL Root Connection"
echo "----------------------------------------"

echo "Testing MySQL root connection (you may need to enter root password):"
if mysql -u root -p -e "SELECT VERSION();" 2>/dev/null; then
    echo "✅ MySQL root connection successful"
else
    echo "❌ MySQL root connection failed"
    echo "You may need to secure MySQL installation:"
    echo "sudo mysql_secure_installation"
fi

# 5. Check if apparel_store database exists
echo ""
echo "📋 Step 5: Checking Database and User"
echo "------------------------------------"

echo "Checking if apparel_store database and user exist..."
echo "You'll need to enter MySQL root password:"

mysql -u root -p << 'EOF'
SHOW DATABASES LIKE 'apparel_store';
SELECT User, Host FROM mysql.user WHERE User = 'apparel_user';
EOF

# 6. Create database and user if needed
echo ""
echo "📋 Step 6: Database and User Setup Commands"
echo "-------------------------------------------"

echo "If the database or user doesn't exist, run these MySQL commands:"
echo ""
echo "mysql -u root -p"
echo ""
echo "Then run these SQL commands:"
echo ""
cat << 'EOF'
-- Create database
CREATE DATABASE IF NOT EXISTS apparel_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (replace 'your_secure_password' with your actual password)
CREATE USER IF NOT EXISTS 'apparel_user'@'localhost' IDENTIFIED BY 'secure_password_here';
CREATE USER IF NOT EXISTS 'apparel_user'@'127.0.0.1' IDENTIFIED BY 'secure_password_here';
CREATE USER IF NOT EXISTS 'apparel_user'@'%' IDENTIFIED BY 'secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'localhost';
GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'127.0.0.1';
GRANT ALL PRIVILEGES ON apparel_store.* TO 'apparel_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Test the user
SELECT User, Host FROM mysql.user WHERE User = 'apparel_user';
SHOW DATABASES;

-- Exit MySQL
EXIT;
EOF

echo ""
echo "📋 Step 7: Test Laravel Database Connection"
echo "-------------------------------------------"

cd /var/www/html || exit 1

echo "Testing Laravel database connection..."
if sudo -u www-data php artisan migrate:status 2>/dev/null; then
    echo "✅ Laravel can connect to database"
else
    echo "❌ Laravel cannot connect to database"
    echo "Check your .env file settings"
fi

# 8. Check .env file
echo ""
echo "📋 Step 8: Current .env Database Settings"
echo "-----------------------------------------"

echo "Current database settings in .env:"
grep -E "^DB_|^MONGODB_" /var/www/html/.env || echo "No database settings found in .env"

echo ""
echo "📋 Step 9: Run Laravel Setup Commands"
echo "------------------------------------"

echo "Running Laravel setup commands..."

# Clear config cache
sudo -u www-data php artisan config:clear

# Run migrations (this will also create sessions table)
echo "Running migrations..."
sudo -u www-data php artisan migrate --force

# Create sessions table specifically if needed
echo "Creating sessions table..."
sudo -u www-data php artisan session:table
sudo -u www-data php artisan migrate --force

# Cache config
sudo -u www-data php artisan config:cache

echo ""
echo "🎉 Database troubleshooting complete!"
echo ""
echo "📋 Summary of what to check:"
echo "1. MySQL service should be running"
echo "2. Database 'apparel_store' should exist"
echo "3. User 'apparel_user' should have proper permissions"
echo "4. .env should have correct DB credentials"
echo "5. Sessions table should be created"
echo ""
echo "🔗 Test your application: http://13.204.86.61"