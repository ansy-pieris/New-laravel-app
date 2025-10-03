<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // /products → Store landing (category grid)
    public function index()
    {
        // Fetch the 4 main categories
        $categories = Category::whereIn('slug', ['men','women','footwear','accessories'])
            ->get(['category_id','name','slug']);

        // Map to include thumbnail images (fallback to public/images/categories/{slug}.jpg)
        $categories = $categories->map(function ($c) {
            return [
                'name'  => $c->name,
                'slug'  => $c->slug,
                'image' => asset('images/categories/'.$c->slug.'.jpg'),
            ];
        });

        return view('products.index', compact('categories'));
    }

    // /products/{category:slug} → Men/Women/Footwear/Accessories
    public function byCategory(Category $category)
    {
        $products = Product::where('is_active', true)
            ->where('category_id', $category->category_id)
            ->latest()
            ->paginate(12);

        return view('products.category', [
            'category'     => $category->slug,
            'categoryName' => $category->name,
            'products'     => $products,
        ]);
    }

    // /product/{product:slug} → Product detail
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
