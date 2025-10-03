# Laravel Website Issues Fix - Run these commands on EC2
# These commands will fix images, logo, categories, and 404 errors

Write-Host "🔧 Laravel Website Issues Fix" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""

Write-Host "📋 SSH into your EC2 and run these commands:" -ForegroundColor Yellow
Write-Host "ssh -i your-key.pem ubuntu@13.204.86.61" -ForegroundColor White
Write-Host ""

$commands = @"
# 1. GO TO PROJECT DIRECTORY
cd /var/www/html

# 2. CREATE STORAGE DIRECTORIES
sudo -u www-data mkdir -p storage/app/public/products
sudo -u www-data mkdir -p public/storage/products
sudo -u www-data mkdir -p public/images/categories

# 3. CREATE STORAGE SYMLINK
sudo -u www-data php artisan storage:link

# 4. SEED DATABASE WITH CATEGORIES AND PRODUCTS
sudo -u www-data php artisan db:seed --class=ProductSeeder

# 5. CREATE LOGO FILE (placeholder)
sudo wget -O public/images/logo.png "https://via.placeholder.com/64x64/000000/FFFFFF?text=ARES"

# 6. CREATE CATEGORY IMAGES
sudo wget -O public/images/categories/men.jpg "https://via.placeholder.com/400x300/333333/FFFFFF?text=Men"
sudo wget -O public/images/categories/women.jpg "https://via.placeholder.com/400x300/333333/FFFFFF?text=Women"
sudo wget -O public/images/categories/footwear.jpg "https://via.placeholder.com/400x300/333333/FFFFFF?text=Footwear"
sudo wget -O public/images/categories/accessories.jpg "https://via.placeholder.com/400x300/333333/FFFFFF?text=Accessories"

# 7. CREATE PRODUCT IMAGES
sudo wget -O storage/app/public/products/1758611952_shirt.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Shirt"
sudo wget -O storage/app/public/products/1758611930_blazer.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Blazer"
sudo wget -O storage/app/public/products/1758612010_shorts.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Shorts"
sudo wget -O storage/app/public/products/1758612034_skirt.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Dress"
sudo wget -O storage/app/public/products/1758622529_crop.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Crop+Top"
sudo wget -O storage/app/public/products/1758611969_jacket.jpg "https://via.placeholder.com/400x400/666666/FFFFFF?text=Jacket"

# 8. CREATE CAROUSEL IMAGES
sudo wget -O public/images/Ares3.jpg "https://via.placeholder.com/1200x500/111111/FFFFFF?text=ARES+Store"
sudo wget -O public/images/hero3.webp "https://via.placeholder.com/1200x500/111111/FFFFFF?text=Fashion"
sudo wget -O public/images/hero2.jpg "https://via.placeholder.com/1200x500/111111/FFFFFF?text=Style"

# 9. FIX PERMISSIONS
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/public/storage
sudo chown -R www-data:www-data /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/storage
sudo chmod -R 775 /var/www/html/storage

# 10. CLEAR AND CACHE
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache

# 11. RESTART APACHE
sudo systemctl restart apache2

# 12. TEST DATABASE
mysql -u apparel_user -p apparel_store -e "SELECT * FROM categories;"
mysql -u apparel_user -p apparel_store -e "SELECT name, image FROM products LIMIT 5;"
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "🚨 ROOT CAUSES IDENTIFIED:" -ForegroundColor Red
Write-Host ""
Write-Host "1. MISSING IMAGES:" -ForegroundColor Yellow
Write-Host "   - Logo file doesn't exist: public/images/logo.png" -ForegroundColor White
Write-Host "   - Product images missing in storage/app/public/products/" -ForegroundColor White
Write-Host "   - Category images missing in public/images/categories/" -ForegroundColor White
Write-Host ""
Write-Host "2. DATABASE NOT SEEDED:" -ForegroundColor Yellow
Write-Host "   - Categories table is empty (causing 404 errors)" -ForegroundColor White
Write-Host "   - Products table might be empty" -ForegroundColor White
Write-Host ""
Write-Host "3. STORAGE SYMLINK MISSING:" -ForegroundColor Yellow
Write-Host "   - public/storage symlink not created" -ForegroundColor White
Write-Host ""

Write-Host "✅ WHAT THE FIX DOES:" -ForegroundColor Green
Write-Host "• Creates database categories (men, women, footwear, accessories)" -ForegroundColor White
Write-Host "• Creates sample products with images" -ForegroundColor White
Write-Host "• Downloads placeholder images for missing files" -ForegroundColor White
Write-Host "• Creates storage symlink for image access" -ForegroundColor White
Write-Host "• Fixes file permissions" -ForegroundColor White
Write-Host "• Clears and rebuilds caches" -ForegroundColor White

Write-Host ""
Write-Host "🌐 AFTER RUNNING THESE COMMANDS, TEST:" -ForegroundColor Blue
Write-Host "Homepage: http://13.204.86.61/ (should show logo and product images)" -ForegroundColor White
Write-Host "Products: http://13.204.86.61/products (should show 4 categories)" -ForegroundColor White
Write-Host "Men: http://13.204.86.61/products/men (should work, no 404)" -ForegroundColor White
Write-Host "Women: http://13.204.86.61/products/women (should work, no 404)" -ForegroundColor White

Write-Host ""
Write-Host "📝 NOTE: Placeholder images will be created. Replace with real images later!" -ForegroundColor Magenta