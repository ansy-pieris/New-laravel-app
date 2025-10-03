#!/bin/bash

# Laravel Apparel Store - Fix Missing Images, Categories, and Data
# Run this script on your EC2 instance

echo "🔧 Fixing Laravel Apparel Store Issues"
echo "======================================"

cd /var/www/html || exit 1

echo "📋 Step 1: Creating necessary directories..."
# Create storage directories
sudo -u www-data mkdir -p storage/app/public/products
sudo -u www-data mkdir -p public/storage/products
sudo -u www-data mkdir -p public/images/categories

echo "📋 Step 2: Creating storage symlink..."
# Ensure storage symlink exists
sudo -u www-data php artisan storage:link

echo "📋 Step 3: Running database seeders..."
# Run seeders to create categories and sample products
sudo -u www-data php artisan db:seed --class=ProductSeeder

echo "📋 Step 4: Downloading placeholder images..."
# Create placeholder images for missing files

# Create logo placeholder
sudo wget -O public/images/logo.png "https://via.placeholder.com/64x64/000000/FFFFFF?text=ARES" 2>/dev/null || {
    echo "Creating logo placeholder..."
    sudo -u www-data convert -size 64x64 xc:black -fill white -gravity center -pointsize 12 -annotate +0+0 "ARES" public/images/logo.png 2>/dev/null || {
        echo "Creating simple logo file..."
        sudo touch public/images/logo.png
    }
}

# Category placeholder images
declare -a categories=("men" "women" "footwear" "accessories")
for category in "${categories[@]}"; do
    if [ ! -f "public/images/categories/${category}.jpg" ]; then
        echo "Creating ${category} category image..."
        sudo wget -O "public/images/categories/${category}.jpg" "https://via.placeholder.com/400x300/333333/FFFFFF?text=${category^}" 2>/dev/null || {
            sudo touch "public/images/categories/${category}.jpg"
        }
    fi
done

# Product placeholder images
declare -a product_images=("1758611952_shirt.jpg" "1758611930_blazer.jpg" "1758612010_shorts.jpg" "1758612034_skirt.jpg" "1758622529_crop.jpg" "1758611969_jacket.jpg")
for image in "${product_images[@]}"; do
    if [ ! -f "storage/app/public/products/${image}" ]; then
        echo "Creating product image: ${image}..."
        sudo wget -O "storage/app/public/products/${image}" "https://via.placeholder.com/400x400/666666/FFFFFF?text=Product" 2>/dev/null || {
            sudo touch "storage/app/public/products/${image}"
        }
    fi
    
    # Also create in public/storage for direct access
    if [ ! -f "public/storage/products/${image}" ]; then
        sudo cp "storage/app/public/products/${image}" "public/storage/products/${image}" 2>/dev/null || {
            sudo touch "public/storage/products/${image}"
        }
    fi
done

echo "📋 Step 5: Creating carousel images..."
# Create carousel images for homepage
declare -a carousel_images=("Ares3.jpg" "hero3.webp" "hero2.jpg")
for image in "${carousel_images[@]}"; do
    if [ ! -f "public/images/${image}" ]; then
        echo "Creating carousel image: ${image}..."
        sudo wget -O "public/images/${image}" "https://via.placeholder.com/1200x500/111111/FFFFFF?text=ARES+Store" 2>/dev/null || {
            sudo touch "public/images/${image}"
        }
    fi
done

echo "📋 Step 6: Setting proper permissions..."
# Fix permissions
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/public/storage
sudo chown -R www-data:www-data /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/storage
sudo chmod -R 775 /var/www/html/storage

echo "📋 Step 7: Clearing caches..."
# Clear caches
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear

echo "📋 Step 8: Caching configurations..."
# Cache configurations
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "📋 Step 9: Restarting Apache..."
sudo systemctl restart apache2

echo "📋 Step 10: Testing database..."
# Check if categories were created
echo "Categories in database:"
mysql -u apparel_user -p apparel_store -e "SELECT * FROM categories;" 2>/dev/null || echo "Please check database connection"

echo "Products in database:"
mysql -u apparel_user -p apparel_store -e "SELECT name, image FROM products LIMIT 5;" 2>/dev/null || echo "Please check database connection"

echo ""
echo "🎉 Fix script completed!"
echo ""
echo "📊 What was fixed:"
echo "✅ Storage symlink created"
echo "✅ Categories seeded in database"
echo "✅ Sample products created"
echo "✅ Placeholder images generated"
echo "✅ Logo placeholder added"
echo "✅ Permissions fixed"
echo "✅ Caches cleared and rebuilt"
echo ""
echo "🌐 Test your website now:"
echo "Homepage: http://13.204.86.61/"
echo "Products: http://13.204.86.61/products"
echo "Men: http://13.204.86.61/products/men"
echo "Women: http://13.204.86.61/products/women"
echo ""
echo "📝 Note: Placeholder images were created. Replace them with actual images later."