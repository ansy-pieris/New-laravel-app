# Laravel Apparel Store - EC2 Troubleshooting Commands (PowerShell)
# Use these commands to troubleshoot the ERR_CONNECTION_REFUSED issue

Write-Host "🔍 Laravel Apparel Store - EC2 Troubleshooting Guide" -ForegroundColor Green
Write-Host ""

Write-Host "📋 SSH into your EC2 instance and run these commands:" -ForegroundColor Yellow
Write-Host ""

# Commands to run on EC2
$commands = @"
# 1. Check Apache status
sudo systemctl status apache2

# 2. Check if Apache is listening on port 80
sudo netstat -tulpn | grep :80
sudo ss -tulpn | grep :80

# 3. Test Apache locally on EC2
curl -I http://localhost
curl -I http://127.0.0.1

# 4. Check Apache configuration syntax
sudo apache2ctl configtest

# 5. Check Apache error logs
sudo tail -20 /var/log/apache2/error.log
sudo tail -20 /var/log/apache2/access.log

# 6. Check Ubuntu firewall (UFW) status
sudo ufw status

# 7. If UFW is active, allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload

# 8. Restart Apache
sudo systemctl restart apache2

# 9. Enable required Apache modules (if not enabled)
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires

# 10. Check if the document root is correct
ls -la /var/www/html/public/

# 11. Test Laravel application
sudo -u www-data php /var/www/html/artisan --version

# 12. Check Laravel logs
sudo tail -20 /var/www/html/storage/logs/laravel.log

# 13. Verify file permissions
ls -la /var/www/html/
ls -la /var/www/html/storage/
ls -la /var/www/html/bootstrap/cache/
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "🔧 Most Common Solutions for ERR_CONNECTION_REFUSED:" -ForegroundColor Green
Write-Host ""
Write-Host "1. AWS Security Group Issue (Most Common):" -ForegroundColor Red
Write-Host "   - Go to AWS EC2 Console" -ForegroundColor White
Write-Host "   - Select your instance" -ForegroundColor White
Write-Host "   - Click Security tab" -ForegroundColor White  
Write-Host "   - Click on the Security Group link" -ForegroundColor White
Write-Host "   - Edit Inbound Rules" -ForegroundColor White
Write-Host "   - Add: Type=HTTP, Protocol=TCP, Port=80, Source=0.0.0.0/0" -ForegroundColor White
Write-Host "   - Add: Type=HTTPS, Protocol=TCP, Port=443, Source=0.0.0.0/0" -ForegroundColor White
Write-Host ""

Write-Host "2. Apache Not Running:" -ForegroundColor Red
Write-Host "   sudo systemctl start apache2" -ForegroundColor White
Write-Host "   sudo systemctl enable apache2" -ForegroundColor White
Write-Host ""

Write-Host "3. Ubuntu Firewall Blocking:" -ForegroundColor Red
Write-Host "   sudo ufw allow 80/tcp" -ForegroundColor White
Write-Host "   sudo ufw allow 443/tcp" -ForegroundColor White
Write-Host ""

Write-Host "4. Wrong Apache Document Root:" -ForegroundColor Red
Write-Host "   Should point to: /var/www/html/public" -ForegroundColor White
Write-Host ""

Write-Host "🌐 Test URLs:" -ForegroundColor Green
Write-Host "External: http://13.204.86.61" -ForegroundColor White
Write-Host "From EC2: curl http://localhost" -ForegroundColor White

Write-Host ""
Write-Host "📞 Contact Information:" -ForegroundColor Blue
Write-Host "If still having issues, check:" -ForegroundColor White
Write-Host "- AWS EC2 Security Groups (most likely issue)" -ForegroundColor White
Write-Host "- Apache error logs on EC2" -ForegroundColor White
Write-Host "- Network connectivity from your location" -ForegroundColor White