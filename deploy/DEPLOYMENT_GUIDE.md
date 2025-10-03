# Laravel Apparel Store Deployment Guide

## Overview
This guide will help you deploy your Laravel Apparel Store application to an Ubuntu 24.04 EC2 instance with Apache web server.

## Prerequisites
- Ubuntu 24.04 EC2 instance
- Root access or sudo privileges
- MongoDB Atlas cluster set up
- Your application files

## 🚨 IMPORTANT: ERR_CONNECTION_REFUSED Troubleshooting

If you get **ERR_CONNECTION_REFUSED** when visiting your site, the issue is most likely:

### 1. AWS Security Group Configuration (Most Common Issue)
**This is the #1 cause of connection refused errors!**

1. Go to AWS EC2 Console
2. Select your instance
3. Click **Security** tab
4. Click on your Security Group link
5. Click **Edit inbound rules**
6. Add these rules:
   - **Type:** HTTP, **Protocol:** TCP, **Port:** 80, **Source:** 0.0.0.0/0
   - **Type:** HTTPS, **Protocol:** TCP, **Port:** 443, **Source:** 0.0.0.0/0
7. Save rules

### 2. Ubuntu Firewall (UFW)
```bash
sudo ufw status
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 3. Apache Service Status
```bash
sudo systemctl status apache2
sudo systemctl start apache2
sudo systemctl enable apache2
```

## Deployment Steps

### 1. Prepare Your Local Files
Ensure you have the following files ready:
- `.env.production` - Production environment configuration
- `deploy/apache-apparel-store.conf` - Apache virtual host configuration
- `deploy/deploy.sh` - Initial server setup script
- `deploy/post-deploy.sh` - Post-deployment configuration script

### 2. Upload Files to EC2
Use SCP or your preferred method to upload your Laravel application to your EC2 instance:

```bash
# From your local machine (PowerShell)
scp -r -i your-key.pem . ubuntu@13.204.86.61:/tmp/apparel_store/
```

### 3. Run Initial Deployment Script
SSH into your EC2 instance and run the deployment script:

```bash
ssh -i your-key.pem ubuntu@13.204.86.61
sudo cp -r /tmp/apparel_store/* /var/www/html/
sudo bash /var/www/html/deploy/deploy.sh
```

### 4. Configure Environment
```bash
# Copy production environment file
sudo cp /var/www/html/.env.production /var/www/html/.env

# Update MongoDB Atlas connection string in .env file
sudo nano /var/www/html/.env
# Update the MONGODB_CONNECTION line with your Atlas connection string
```

### 5. Configure Apache
```bash
# Enable the site
sudo cp /var/www/html/deploy/apache-apparel-store.conf /etc/apache2/sites-available/
sudo a2ensite apache-apparel-store.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

### 6. Run Post-Deployment Script
```bash
sudo bash /var/www/html/deploy/post-deploy.sh
```

### 7. Secure MySQL Installation
```bash
sudo mysql_secure_installation
```

### 8. Configure Firewall (Optional but Recommended)
```bash
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
sudo ufw enable
```

## Important Configuration Updates

### MongoDB Atlas Connection
Update your `.env` file with your actual MongoDB Atlas connection string:
```
MONGODB_CONNECTION=mongodb+srv://username:password@cluster.mongodb.net/apparel_store?retryWrites=true&w=majority
```

### MySQL Database
The deployment script creates:
- Database: `apparel_store`
- User: `apparel_user`
- Password: `secure_password_here` (change this!)

### Email Configuration
Update email settings in `.env` for production use:
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## Testing Your Deployment

1. Visit `http://13.204.86.61` in your browser
2. Check that the application loads correctly
3. Test user registration and login
4. Verify MongoDB connectivity for products/orders
5. Test MySQL connectivity for user authentication

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/html
   sudo chmod -R 755 /var/www/html
   sudo chmod -R 775 /var/www/html/storage
   sudo chmod -R 775 /var/www/html/bootstrap/cache
   ```

2. **MongoDB Extension Not Loading**
   ```bash
   sudo pecl install mongodb
   echo "extension=mongodb.so" | sudo tee -a /etc/php/8.3/apache2/php.ini
   sudo systemctl restart apache2
   ```

3. **Application Key Error**
   ```bash
   cd /var/www/html
   sudo php artisan key:generate
   ```

4. **Cache Issues**
   ```bash
   cd /var/www/html
   sudo php artisan cache:clear
   sudo php artisan config:clear
   sudo php artisan view:clear
   ```

### Log Files
- Apache Error Log: `/var/log/apache2/apparel_store_error.log`
- Apache Access Log: `/var/log/apache2/apparel_store_access.log`
- Laravel Log: `/var/www/html/storage/logs/laravel.log`

## Security Recommendations

1. **Set up SSL/TLS certificate** using Let's Encrypt
2. **Change default MySQL password** for the apparel_user
3. **Configure fail2ban** for SSH protection
4. **Regular system updates**
5. **Backup strategy** for database and files

## Production Optimizations

1. **Enable OPcache** in PHP
2. **Set up Redis** for caching (optional)
3. **Configure log rotation**
4. **Set up monitoring** and alerting
5. **Database optimization** and indexing

Your Laravel Apparel Store should now be successfully deployed and accessible at http://13.204.86.61!