<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products or latest products for homepage
        $featuredProducts = Product::where('is_featured', true)
            ->orWhere('created_at', '>=', now()->subDays(30))
            ->take(8)
            ->get();
        
        $categories = Category::all();
        
        return view('pages.home', compact('featuredProducts', 'categories'));
    }
}