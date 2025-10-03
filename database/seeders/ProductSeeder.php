<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Men', 'slug' => 'men'],
            ['name' => 'Women', 'slug' => 'women'],
            ['name' => 'Footwear', 'slug' => 'footwear'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Create sample products
        $menCategory = Category::where('slug', 'men')->first();
        $womenCategory = Category::where('slug', 'women')->first();
        
        if ($menCategory) {
            Product::updateOrCreate(['slug' => 'mens-t-shirt'], [
                'category_id' => $menCategory->category_id,
                'name' => "Men's Classic T-Shirt",
                'slug' => 'mens-t-shirt',
                'description' => 'Comfortable cotton t-shirt for everyday wear.',
                'price' => 2500.00,
                'stock' => 50,
                'image' => '1758611952_shirt.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => true,
            ]);

            Product::updateOrCreate(['slug' => 'mens-blazer'], [
                'category_id' => $menCategory->category_id,
                'name' => "Men's Professional Blazer",
                'slug' => 'mens-blazer',
                'description' => 'Stylish blazer perfect for office and formal occasions.',
                'price' => 8500.00,
                'stock' => 20,
                'image' => '1758611930_blazer.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => true,
            ]);

            Product::updateOrCreate(['slug' => 'mens-shorts'], [
                'category_id' => $menCategory->category_id,
                'name' => "Men's Casual Shorts",
                'slug' => 'mens-shorts',
                'description' => 'Comfortable shorts for casual and sports activities.',
                'price' => 1800.00,
                'stock' => 35,
                'image' => '1758612010_shorts.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => false,
            ]);
        }

        if ($womenCategory) {
            Product::updateOrCreate(['slug' => 'womens-dress'], [
                'category_id' => $womenCategory->category_id,
                'name' => "Women's Summer Dress",
                'slug' => 'womens-dress',
                'description' => 'Elegant summer dress perfect for any occasion.',
                'price' => 4500.00,
                'stock' => 25,
                'image' => '1758612034_skirt.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => false,
            ]);

            Product::updateOrCreate(['slug' => 'womens-crop-top'], [
                'category_id' => $womenCategory->category_id,
                'name' => "Women's Stylish Crop Top",
                'slug' => 'womens-crop-top',
                'description' => 'Trendy crop top perfect for casual outings.',
                'price' => 2200.00,
                'stock' => 40,
                'image' => '1758622529_crop.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => true,
            ]);

            Product::updateOrCreate(['slug' => 'womens-jacket'], [
                'category_id' => $womenCategory->category_id,
                'name' => "Women's Winter Jacket",
                'slug' => 'womens-jacket',
                'description' => 'Warm and stylish jacket for winter season.',
                'price' => 6500.00,
                'stock' => 15,
                'image' => '1758611969_jacket.jpg', // Use existing uploaded image
                'is_active' => true,
                'is_featured' => true,
            ]);
        }
    }
}