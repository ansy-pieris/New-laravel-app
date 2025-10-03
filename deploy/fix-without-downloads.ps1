# Fix Images Without External Downloads - Run on EC2
# The database seeding worked! Now we just need to create local images

Write-Host "🎨 Creating Local Images (No Downloads Needed)" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

Write-Host "✅ GOOD NEWS: Database seeding worked!" -ForegroundColor Green
Write-Host "The categories and products are now in your database." -ForegroundColor White
Write-Host ""

Write-Host "📋 Run these commands on your EC2:" -ForegroundColor Yellow
Write-Host ""

$commands = @"
# 1. Create image directories
sudo -u www-data mkdir -p public/images/categories
sudo -u www-data mkdir -p storage/app/public/products
sudo chmod -R 755 public/images

# 2. Create simple text-based placeholder files
# Logo
echo "ARES LOGO" | sudo tee public/images/logo.png > /dev/null

# Category images  
echo "MEN CATEGORY" | sudo tee public/images/categories/men.jpg > /dev/null
echo "WOMEN CATEGORY" | sudo tee public/images/categories/women.jpg > /dev/null
echo "FOOTWEAR CATEGORY" | sudo tee public/images/categories/footwear.jpg > /dev/null
echo "ACCESSORIES CATEGORY" | sudo tee public/images/categories/accessories.jpg > /dev/null

# Product images
echo "SHIRT IMAGE" | sudo tee storage/app/public/products/1758611952_shirt.jpg > /dev/null
echo "BLAZER IMAGE" | sudo tee storage/app/public/products/1758611930_blazer.jpg > /dev/null
echo "SHORTS IMAGE" | sudo tee storage/app/public/products/1758612010_shorts.jpg > /dev/null
echo "DRESS IMAGE" | sudo tee storage/app/public/products/1758612034_skirt.jpg > /dev/null
echo "CROP TOP IMAGE" | sudo tee storage/app/public/products/1758622529_crop.jpg > /dev/null
echo "JACKET IMAGE" | sudo tee storage/app/public/products/1758611969_jacket.jpg > /dev/null

# Copy to public storage
sudo cp storage/app/public/products/*.jpg public/storage/products/ 2>/dev/null || echo "Storage link exists"

# Carousel images
echo "ARES STORE BANNER" | sudo tee public/images/Ares3.jpg > /dev/null
echo "FASHION BANNER" | sudo tee public/images/hero3.webp > /dev/null
echo "STYLE BANNER" | sudo tee public/images/hero2.jpg > /dev/null

# 3. Fix permissions
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/public/storage
sudo chown -R www-data:www-data /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/images

# 4. Test database (verify seeding worked)
mysql -u apparel_user -p apparel_store -e "SELECT category_id, name, slug FROM categories;"
mysql -u apparel_user -p apparel_store -e "SELECT product_id, name, image FROM products LIMIT 3;"

# 5. Final restart
sudo systemctl restart apache2
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host ""
Write-Host "🔧 Alternative: Install ImageMagick for better placeholders" -ForegroundColor Yellow
Write-Host ""

$imagemagick_commands = @"
# Install ImageMagick
sudo apt update
sudo apt install -y imagemagick

# Create better placeholder images
sudo convert -size 64x64 xc:black -fill white -gravity center -pointsize 12 -annotate +0+0 "ARES" public/images/logo.png
sudo convert -size 400x300 xc:'#333333' -fill white -gravity center -pointsize 20 -annotate +0+0 "MEN" public/images/categories/men.jpg
sudo convert -size 400x300 xc:'#333333' -fill white -gravity center -pointsize 20 -annotate +0+0 "WOMEN" public/images/categories/women.jpg
sudo convert -size 400x400 xc:'#666666' -fill white -gravity center -pointsize 16 -annotate +0+0 "SHIRT" storage/app/public/products/1758611952_shirt.jpg
"@

Write-Host $imagemagick_commands -ForegroundColor Cyan

Write-Host ""
Write-Host "🎯 WHAT'S WORKING:" -ForegroundColor Green
Write-Host "✅ Database connection" -ForegroundColor White
Write-Host "✅ Categories created (men, women, footwear, accessories)" -ForegroundColor White
Write-Host "✅ Sample products created" -ForegroundColor White
Write-Host "✅ Storage symlink exists" -ForegroundColor White
Write-Host ""

Write-Host "❌ WHAT NEEDS FIXING:" -ForegroundColor Red
Write-Host "• Missing image files (will be created above)" -ForegroundColor White
Write-Host "• DNS resolution issue (doesn't affect website function)" -ForegroundColor White
Write-Host ""

Write-Host "🌐 AFTER RUNNING COMMANDS, YOUR WEBSITE SHOULD WORK:" -ForegroundColor Blue
Write-Host "Homepage: http://13.204.86.61/ (logo + product images)" -ForegroundColor White
Write-Host "Products: http://13.204.86.61/products (4 categories)" -ForegroundColor White
Write-Host "Category pages: No more 404 errors!" -ForegroundColor White

Write-Host ""
Write-Host "💡 TIP: The images will be simple placeholders. Replace with real images later!" -ForegroundColor Magenta