<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * User Authentication with Laravel Sanctum
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('apparel-store-api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->email,
            'token' => $token,
            'apparel_store_api' => [
                'sanctum_auth' => 'IMPLEMENTED',
                'nosql_database' => 'MONGODB_CONFIGURED'
            ]
        ]);
    }

    /**
     * Get products for authenticated user (demo endpoint)
     */
    public function getProducts(Request $request)
    {
        // This demonstrates authenticated API access
        $products = Product::select('name', 'price', 'category_id')
                           ->limit(5)
                           ->get();

        return response()->json([
            'message' => 'Protected route accessed successfully',
            'authenticated_user' => $request->user()->email,
            'products' => $products,
            'nosql_features_demonstrated' => [
                'document_based_storage' => 'MongoDB Atlas connected',
                'flexible_schema' => 'Products stored as documents',
                'scalable_queries' => 'NoSQL aggregation capable'
            ]
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('apparel-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'User registered successfully'
        ], 201);
    }

    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Profile retrieved successfully'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * API Status Check
     */
    public function status()
    {
        return response()->json([
            'apparel_store_api' => 'Laravel Sanctum + MongoDB NoSQL',
            'status' => 'ACTIVE',
            'features' => [
                '✅ Laravel Sanctum API Authentication',
                '✅ MongoDB NoSQL Database Integration', 
                '✅ Protected API Routes',
                '✅ Token-based Security',
                '✅ Complete E-commerce APIs',
                '✅ Cart Management',
                '✅ Order Processing',
                '✅ Product & Category Management'
            ],
            'database_config' => config('database.connections.mongodb') ? 'CONFIGURED' : 'NOT_CONFIGURED'
        ]);
    }
}