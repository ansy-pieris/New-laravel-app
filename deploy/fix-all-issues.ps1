# Laravel Issues Fix - Run these commands on EC2
# Fixes: Missing images, logo, cart counter, checkout errors, cart product images

Write-Host "🔧 Laravel Issues Comprehensive Fix" -ForegroundColor Green
Write-Host "===================================" -ForegroundColor Green
Write-Host ""

Write-Host "📋 ISSUES IDENTIFIED AND FIXED:" -ForegroundColor Yellow
Write-Host "1. .env file was corrupted (FIXED in repository)" -ForegroundColor White
Write-Host "2. Product images missing (WILL BE CREATED)" -ForegroundColor White  
Write-Host "3. Logo not showing (WILL BE CREATED)" -ForegroundColor White
Write-Host "4. Cart counter not updating (FIXED in code)" -ForegroundColor White
Write-Host "5. Checkout errors (IMPROVED error handling)" -ForegroundColor White
Write-Host "6. Cart showing logos instead of product images (FIXED)" -ForegroundColor White
Write-Host ""

Write-Host "📋 Run these commands on your EC2:" -ForegroundColor Yellow
Write-Host ""

$commands = @"
# 1. PULL LATEST CODE WITH FIXES
cd /var/www/html
sudo git pull origin main

# 2. INSTALL IMAGEMAGICK FOR CREATING IMAGES
sudo apt update
sudo apt install -y imagemagick

# 3. CREATE LOGO
sudo convert -size 128x128 xc:black -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "ARES" public/images/logo.png

# 4. CREATE CATEGORY IMAGES
sudo mkdir -p public/images/categories
sudo convert -size 400x300 xc:'#333333' -fill white -gravity center -pointsize 24 -font Arial -annotate +0+0 "MEN" public/images/categories/men.jpg
sudo convert -size 400x300 xc:'#444444' -fill white -gravity center -pointsize 24 -font Arial -annotate +0+0 "WOMEN" public/images/categories/women.jpg
sudo convert -size 400x300 xc:'#555555' -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "FOOTWEAR" public/images/categories/footwear.jpg
sudo convert -size 400x300 xc:'#666666' -fill white -gravity center -pointsize 18 -font Arial -annotate +0+0 "ACCESSORIES" public/images/categories/accessories.jpg

# 5. CREATE PRODUCT IMAGES (different colors)
sudo mkdir -p storage/app/public/products
sudo convert -size 400x400 xc:'#8B4513' -fill white -gravity center -pointsize 24 -font Arial -annotate +0+0 "SHIRT" storage/app/public/products/1758611952_shirt.jpg
sudo convert -size 400x400 xc:'#2F4F4F' -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "BLAZER" storage/app/public/products/1758611930_blazer.jpg
sudo convert -size 400x400 xc:'#4682B4' -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "SHORTS" storage/app/public/products/1758612010_shorts.jpg
sudo convert -size 400x400 xc:'#9932CC' -fill white -gravity center -pointsize 22 -font Arial -annotate +0+0 "DRESS" storage/app/public/products/1758612034_skirt.jpg
sudo convert -size 400x400 xc:'#FF6347' -fill white -gravity center -pointsize 18 -font Arial -annotate +0+0 "CROP TOP" storage/app/public/products/1758622529_crop.jpg
sudo convert -size 400x400 xc:'#800080' -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "JACKET" storage/app/public/products/1758611969_jacket.jpg

# 6. CREATE PLACEHOLDER IMAGE
sudo convert -size 400x400 xc:'#999999' -fill white -gravity center -pointsize 16 -font Arial -annotate +0+0 "NO IMAGE" public/images/placeholder.jpg

# 7. CREATE CAROUSEL IMAGES
sudo convert -size 1200x500 xc:'#111111' -fill white -gravity center -pointsize 48 -font Arial -annotate +0+0 "ARES STORE" public/images/Ares3.jpg
sudo convert -size 1200x500 xc:'#222222' -fill white -gravity center -pointsize 48 -font Arial -annotate +0+0 "FASHION" public/images/hero3.webp
sudo convert -size 1200x500 xc:'#333333' -fill white -gravity center -pointsize 48 -font Arial -annotate +0+0 "STYLE" public/images/hero2.jpg

# 8. FIX STORAGE SYMLINK
sudo -u www-data php artisan storage:link --force

# 9. COPY PRODUCT IMAGES TO PUBLIC
sudo cp -r storage/app/public/products/* public/storage/products/

# 10. FIX PERMISSIONS
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/public/storage
sudo chown -R www-data:www-data /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/storage

# 11. CLEAR CACHES
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear

# 12. REBUILD CACHES
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan optimize

# 13. RESTART SERVICES
sudo systemctl restart apache2
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "✅ WHAT THESE COMMANDS WILL FIX:" -ForegroundColor Green
Write-Host ""
Write-Host "🖼️  IMAGES:" -ForegroundColor Yellow
Write-Host "• Logo will appear in navbar" -ForegroundColor White
Write-Host "• Product images will show on product pages" -ForegroundColor White
Write-Host "• Cart will show product images, not logos" -ForegroundColor White
Write-Host "• Category images will display properly" -ForegroundColor White
Write-Host ""
Write-Host "🛒 CART FUNCTIONALITY:" -ForegroundColor Yellow
Write-Host "• Cart counter updates without page refresh" -ForegroundColor White
Write-Host "• Product images display correctly in cart" -ForegroundColor White
Write-Host "• Quantity changes reflect immediately" -ForegroundColor White
Write-Host ""
Write-Host "💳 CHECKOUT:" -ForegroundColor Yellow
Write-Host "• Better error messages instead of generic 'something went wrong'" -ForegroundColor White
Write-Host "• Order placement success will redirect properly" -ForegroundColor White
Write-Host "• Database errors handled more gracefully" -ForegroundColor White
Write-Host ""

Write-Host "🌐 AFTER RUNNING COMMANDS, TEST:" -ForegroundColor Blue
Write-Host "• Homepage: http://13.204.86.61/ (logo + carousel)" -ForegroundColor White
Write-Host "• Men products: http://13.204.86.61/products/men (product images)" -ForegroundColor White  
Write-Host "• Add to cart: Cart counter should update immediately" -ForegroundColor White
Write-Host "• Cart page: http://13.204.86.61/cart (product images, not logos)" -ForegroundColor White
Write-Host "• Checkout: http://13.204.86.61/checkout (better error handling)" -ForegroundColor White

Write-Host ""
Write-Host "📝 NOTE: Images are colorful placeholders. Replace with real images later!" -ForegroundColor Magenta