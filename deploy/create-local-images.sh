#!/bin/bash

# Create Local Placeholder Images - No External Downloads Required
# Run this on your EC2 instance

echo "🎨 Creating Local Placeholder Images"
echo "===================================="

cd /var/www/html || exit 1

echo "📋 Step 1: Creating image directories..."
sudo -u www-data mkdir -p public/images/categories
sudo -u www-data mkdir -p storage/app/public/products
sudo -u www-data mkdir -p public/storage/products

echo "📋 Step 2: Creating simple placeholder images using convert (ImageMagick)..."

# Install ImageMagick if not present
if ! command -v convert &> /dev/null; then
    echo "Installing ImageMagick..."
    sudo apt update
    sudo apt install -y imagemagick
fi

# Create logo
echo "Creating logo..."
sudo convert -size 64x64 xc:black -fill white -gravity center -pointsize 14 -font Arial -annotate +0+0 "ARES" public/images/logo.png 2>/dev/null || {
    # Fallback: create simple text file if ImageMagick fails
    echo "Creating simple logo placeholder..."
    sudo touch public/images/logo.png
}

# Create category images
declare -a categories=("men" "women" "footwear" "accessories")
for category in "${categories[@]}"; do
    echo "Creating ${category} category image..."
    sudo convert -size 400x300 xc:'#333333' -fill white -gravity center -pointsize 24 -font Arial -annotate +0+0 "${category^}" "public/images/categories/${category}.jpg" 2>/dev/null || {
        sudo touch "public/images/categories/${category}.jpg"
    }
done

# Create product images
declare -a products=(
    "1758611952_shirt.jpg:Shirt"
    "1758611930_blazer.jpg:Blazer" 
    "1758612010_shorts.jpg:Shorts"
    "1758612034_skirt.jpg:Dress"
    "1758622529_crop.jpg:Crop Top"
    "1758611969_jacket.jpg:Jacket"
)

for product_info in "${products[@]}"; do
    IFS=':' read -r filename label <<< "$product_info"
    echo "Creating product image: ${filename}..."
    
    # Create in storage/app/public/products
    sudo convert -size 400x400 xc:'#666666' -fill white -gravity center -pointsize 20 -font Arial -annotate +0+0 "$label" "storage/app/public/products/${filename}" 2>/dev/null || {
        sudo touch "storage/app/public/products/${filename}"
    }
    
    # Also copy to public/storage/products for direct access
    sudo cp "storage/app/public/products/${filename}" "public/storage/products/${filename}" 2>/dev/null || {
        sudo touch "public/storage/products/${filename}"
    }
done

# Create carousel images
declare -a carousel=(
    "Ares3.jpg:ARES STORE"
    "hero3.webp:FASHION"
    "hero2.jpg:STYLE"
)

for carousel_info in "${carousel[@]}"; do
    IFS=':' read -r filename label <<< "$carousel_info"
    echo "Creating carousel image: ${filename}..."
    sudo convert -size 1200x500 xc:'#111111' -fill white -gravity center -pointsize 36 -font Arial -annotate +0+0 "$label" "public/images/${filename}" 2>/dev/null || {
        sudo touch "public/images/${filename}"
    }
done

echo "📋 Step 3: Creating CSS-based fallback images..."
# Create a simple HTML file that generates colored rectangles for testing
cat > /tmp/create_simple_images.php << 'EOF'
<?php
// Simple script to create basic colored rectangles as images

function createSimpleImage($width, $height, $text, $filename) {
    // Create a simple colored rectangle image using GD
    if (function_exists('imagecreate')) {
        $image = imagecreate($width, $height);
        $background = imagecolorallocate($image, 102, 102, 102); // Gray background
        $text_color = imagecolorallocate($image, 255, 255, 255); // White text
        
        // Add text
        $font_size = min($width/20, $height/20);
        imagestring($image, 5, $width/2 - strlen($text)*5, $height/2 - 8, $text, $text_color);
        
        // Save image
        imagejpeg($image, $filename, 80);
        imagedestroy($image);
        return true;
    }
    return false;
}

// Create images
echo "Creating images with PHP GD...\n";

// Logo
createSimpleImage(64, 64, "ARES", "/var/www/html/public/images/logo.png");

// Categories
createSimpleImage(400, 300, "MEN", "/var/www/html/public/images/categories/men.jpg");
createSimpleImage(400, 300, "WOMEN", "/var/www/html/public/images/categories/women.jpg");
createSimpleImage(400, 300, "FOOTWEAR", "/var/www/html/public/images/categories/footwear.jpg");
createSimpleImage(400, 300, "ACCESSORIES", "/var/www/html/public/images/categories/accessories.jpg");

// Products
createSimpleImage(400, 400, "SHIRT", "/var/www/html/storage/app/public/products/1758611952_shirt.jpg");
createSimpleImage(400, 400, "BLAZER", "/var/www/html/storage/app/public/products/1758611930_blazer.jpg");
createSimpleImage(400, 400, "SHORTS", "/var/www/html/storage/app/public/products/1758612010_shorts.jpg");
createSimpleImage(400, 400, "DRESS", "/var/www/html/storage/app/public/products/1758612034_skirt.jpg");
createSimpleImage(400, 400, "CROP TOP", "/var/www/html/storage/app/public/products/1758622529_crop.jpg");
createSimpleImage(400, 400, "JACKET", "/var/www/html/storage/app/public/products/1758611969_jacket.jpg");

// Copy to public storage
copy("/var/www/html/storage/app/public/products/1758611952_shirt.jpg", "/var/www/html/public/storage/products/1758611952_shirt.jpg");
copy("/var/www/html/storage/app/public/products/1758611930_blazer.jpg", "/var/www/html/public/storage/products/1758611930_blazer.jpg");
copy("/var/www/html/storage/app/public/products/1758612010_shorts.jpg", "/var/www/html/public/storage/products/1758612010_shorts.jpg");
copy("/var/www/html/storage/app/public/products/1758612034_skirt.jpg", "/var/www/html/public/storage/products/1758612034_skirt.jpg");
copy("/var/www/html/storage/app/public/products/1758622529_crop.jpg", "/var/www/html/public/storage/products/1758622529_crop.jpg");
copy("/var/www/html/storage/app/public/products/1758611969_jacket.jpg", "/var/www/html/public/storage/products/1758611969_jacket.jpg");

// Carousel
createSimpleImage(1200, 500, "ARES STORE", "/var/www/html/public/images/Ares3.jpg");
createSimpleImage(1200, 500, "FASHION", "/var/www/html/public/images/hero3.webp");
createSimpleImage(1200, 500, "STYLE", "/var/www/html/public/images/hero2.jpg");

echo "Images created successfully!\n";
?>
EOF

echo "📋 Step 4: Running PHP script to create images..."
sudo php /tmp/create_simple_images.php

echo "📋 Step 5: Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/public/storage
sudo chown -R www-data:www-data /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/images
sudo chmod -R 755 /var/www/html/public/storage
sudo chmod -R 775 /var/www/html/storage

echo "📋 Step 6: Cleaning up temporary files..."
sudo rm -f /tmp/create_simple_images.php

echo "📋 Step 7: Testing database..."
echo "Categories in database:"
mysql -u apparel_user -p apparel_store -e "SELECT category_id, name, slug FROM categories;" 2>/dev/null || echo "Database connection issue"

echo "Products in database:"
mysql -u apparel_user -p apparel_store -e "SELECT product_id, name, image, is_featured FROM products LIMIT 5;" 2>/dev/null || echo "Database connection issue"

echo "📋 Step 8: Final cache clear and restart..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo systemctl restart apache2

echo ""
echo "🎉 Local image creation completed!"
echo ""
echo "✅ What was created:"
echo "• Logo: public/images/logo.png"
echo "• Category images: public/images/categories/*.jpg"  
echo "• Product images: storage/app/public/products/*.jpg"
echo "• Carousel images: public/images/*.jpg"
echo ""
echo "🌐 Test your website now:"
echo "Homepage: http://13.204.86.61/"
echo "Products: http://13.204.86.61/products"
echo "Men: http://13.204.86.61/products/men"
echo ""
echo "📝 The images are simple placeholders. Replace with real images later!"