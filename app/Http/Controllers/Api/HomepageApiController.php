<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomepageApiController extends Controller
{
    /**
     * Get homepage data for mobile app
     * Returns the same data structure as the web homepage
     */
    public function index()
    {
        // Get the 4 main categories with images (same as web HomeController)
        $categories = Category::whereIn('slug', ['men','women','footwear','accessories'])
            ->get(['category_id','name','slug']);

        // Map categories with image URLs (same logic as web)
        $categoriesData = $categories->map(function ($c) {
            return [
                'id' => $c->category_id,
                'name' => $c->name,
                'slug' => $c->slug,
                'image' => asset('images/categories/' . $c->slug . '.jpg'),
                'route' => '/products/' . $c->slug
            ];
        });

        // Get featured products (same logic as web HomeController)
        $featuredProducts = Product::where('is_featured', true)
            ->orWhere('created_at', '>=', now()->subDays(30))
            ->with('category')
            ->take(8)
            ->get();

        // Format featured products with full data
        $featuredProductsData = $featuredProducts->map(function ($product) {
            return [
                'id' => $product->product_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'formatted_price' => 'Rs. ' . number_format($product->price, 2),
                'image' => $product->image ? asset('storage/products/' . $product->image) : asset('images/placeholder.jpg'),
                'category' => [
                    'id' => $product->category->category_id ?? null,
                    'name' => $product->category->name ?? 'Uncategorized',
                    'slug' => $product->category->slug ?? null,
                ],
                'is_featured' => (bool) $product->is_featured,
                'route' => '/product/' . $product->slug
            ];
        });

        // Carousel/Hero images (same as web homepage)
        $carouselImages = [
            [
                'id' => 1,
                'image' => asset('images/Ares3.jpg'),
                'alt' => 'Slide 1',
                'title' => 'ARES Collection',
                'subtitle' => 'Where power meets fashion'
            ],
            [
                'id' => 2,
                'image' => asset('images/hero3.webp'),
                'alt' => 'Slide 2',
                'title' => 'New Arrivals',
                'subtitle' => 'Discover the latest trends'
            ],
            [
                'id' => 3,
                'image' => asset('images/hero2.jpg'),
                'alt' => 'Slide 3',
                'title' => 'Style & Comfort',
                'subtitle' => 'Perfect for every occasion'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'carousel' => $carouselImages,
                'categories' => $categoriesData,
                'featured_products' => $featuredProductsData,
                'app_info' => [
                    'title' => 'ARES',
                    'welcome_message' => 'Where power meets fashion. Discover bold apparel, empowering accessories, and footwear designed to make you stand out.'
                ]
            ],
            'message' => 'Homepage data retrieved successfully'
        ]);
    }
}