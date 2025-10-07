<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    /**
     * Get all products with pagination (enhanced for mobile app)
     * Updated: Now includes descriptions and all essential fields
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12); // Match web pagination
        $category = $request->get('category');
        $featured = $request->get('featured'); // Add featured filter
        
        $query = Product::with('category')->where('is_active', true);
        
        // Filter by category if provided
        if ($category) {
            $categoryModel = Category::where('slug', $category)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->category_id);
            }
        }
        
        // Filter for featured products if requested
        if ($featured === 'true' || $featured === '1') {
            $query->where(function($q) {
                $q->where('is_featured', true)
                  ->orWhere('created_at', '>=', now()->subDays(30));
            })->take(8);
        }
        
        $products = $query->latest()->paginate($perPage);

        // Transform products to match web data structure
        $transformedProducts = $products->getCollection()->map(function ($product) {
            return $this->formatProductData($product);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $transformedProducts,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * Get single product details (enhanced for mobile app)
     */
    public function show($id)
    {
        try {
            // Try to find product by ID first
            $product = Product::with('category')->find($id);
            
            // If not found by ID, try to find by slug (in case slug was passed instead of ID)
            if (!$product) {
                $product = Product::with('category')->where('slug', $id)->first();
            }

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product with ID or slug '{$id}' not found",
                    'debug' => [
                        'requested_id' => $id,
                        'available_ids' => Product::pluck('product_id')->toArray()
                    ]
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProductData($product, true),
            'message' => 'Product retrieved successfully'
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $products = Product::with('category');

        if ($query) {
            $products->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
        }

        if ($category) {
            $products->where('category_id', $category);
        }

        if ($minPrice) {
            $products->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $products->where('price', '<=', $maxPrice);
        }

        $results = $products->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $results,
            'search_params' => [
                'query' => $query,
                'category' => $category,
                'price_range' => [$minPrice, $maxPrice]
            ]
        ]);
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        try {
            $products = Product::with('category')
                ->where('is_featured', true)
                ->orWhere('created_at', '>=', now()->subDays(30))
                ->take(8)
                ->get();

            $transformedProducts = $products->map(function ($product) {
                return $this->formatProductData($product);
            });

            return response()->json([
                'success' => true,
                'data' => $transformedProducts,
                'message' => 'Featured products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new product (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
            'message' => 'Product created successfully'
        ], 201);
    }

    /**
     * Update product (Admin only)
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'category_id' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->has('name')) $product->name = $request->name;
        if ($request->has('description')) $product->description = $request->description;
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('category_id')) $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
            'message' => 'Product updated successfully'
        ]);
    }

    /**
     * Delete product (Admin only)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Delete product image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Format product data to match web application structure
     */
    private function formatProductData($product, $includeDetails = false)
    {
        // Generate simple image URL for mobile app
        $imageUrl = $product->image 
            ? asset('storage/products/' . $product->image)
            : asset('images/placeholder.jpg');

        $data = [
            'id' => $product->product_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description ?? '', // Always include description
            'price' => (float) $product->price,
            'formatted_price' => 'Rs. ' . number_format($product->price, 2),
            'image' => $imageUrl,
            'stock' => $product->stock ?? 0, // Always include stock
            'stock_status' => $this->getStockStatus($product->stock ?? 0), // Always include stock status
            'is_featured' => (bool) $product->is_featured, // Always include featured status
            'category' => [
                'id' => $product->category->category_id ?? null,
                'name' => $product->category->name ?? 'Uncategorized',
                'slug' => $product->category->slug ?? null,
            ],
        ];

        if ($includeDetails) {
            // Add detailed information for product detail page
            $data = array_merge($data, [
                'description' => $product->description,
                'stock' => $product->stock,
                'stock_status' => $this->getStockStatus($product->stock),
                'is_active' => (bool) $product->is_active,
                'is_featured' => (bool) $product->is_featured,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        return $data;
    }

    /**
     * Get stock status message (same logic as web views)
     */
    private function getStockStatus($stock)
    {
        if ($stock <= 0) {
            return 'Out of stock';
        } elseif ($stock < 10) {
            return "Only {$stock} left in stock";
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get products by category (matches web category page)
     */
    public function byCategory($slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => "Category with slug '{$slug}' not found"
                ], 404);
            }

        $products = Product::where('is_active', true)
            ->where('category_id', $category->category_id)
            ->with('category')
            ->latest()
            ->paginate(12);

        // Hero images for categories (same as web)
        $heroes = [
            'men' => [
                'img' => asset('images/heroes/men.jpg'),
                'title' => "MEN'S WARDROBE",
                'subtitle' => 'Bold fits for every day.',
            ],
            'women' => [
                'img' => asset('images/heroes/women.jpg'),
                'title' => "WOMEN'S WARDROBE",
                'subtitle' => 'Statement pieces & everyday essentials.',
            ],
            'footwear' => [
                'img' => asset('images/heroes/sneakers.jpeg'),
                'title' => 'FOOTWEAR',
                'subtitle' => 'Step into comfort and style.',
            ],
            'accessories' => [
                'img' => asset('images/heroes/watch.jpg'),
                'title' => 'ACCESSORIES',
                'subtitle' => 'Finish your look with the right detail.',
            ],
        ];

        $hero = $heroes[$slug] ?? [
            'img' => asset('images/heroes/default.jpg'),
            'title' => strtoupper($category->name),
            'subtitle' => '',
        ];

        $transformedProducts = $products->getCollection()->map(function ($product) {
            return $this->formatProductData($product);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->category_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'hero' => $hero
                ],
                'products' => $transformedProducts,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ],
            'message' => 'Category products retrieved successfully'
        ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category products: ' . $e->getMessage()
            ], 500);
        }
    }
}