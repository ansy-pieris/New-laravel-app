<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get single category with products
     */
    public function show($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->take(12);
        }])->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Get products by category
     */
    public function getProducts(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $products = Product::where('category_id', $id)
                          ->orderBy($sortBy, $sortOrder)
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'products' => $products
            ],
            'message' => 'Category products retrieved successfully'
        ]);
    }

    /**
     * Get category statistics
     */
    public function getStats($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $stats = [
            'total_products' => $category->products()->count(),
            'avg_price' => $category->products()->avg('price'),
            'min_price' => $category->products()->min('price'),
            'max_price' => $category->products()->max('price'),
            'price_ranges' => [
                'under_50' => $category->products()->where('price', '<', 50)->count(),
                '50_to_100' => $category->products()->whereBetween('price', [50, 100])->count(),
                '100_to_200' => $category->products()->whereBetween('price', [100, 200])->count(),
                'over_200' => $category->products()->where('price', '>', 200)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'statistics' => $stats
            ],
            'message' => 'Category statistics retrieved successfully'
        ]);
    }

    /**
     * Store new category (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Update category (Admin only)
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'name' => 'string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        if ($request->has('name')) $category->name = $request->name;
        if ($request->has('description')) $category->description = $request->description;

        $category->save();

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Delete category (Admin only)
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing products'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}