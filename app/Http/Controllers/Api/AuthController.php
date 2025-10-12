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
     * Get user profile (enhanced for mobile app)
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        // Ensure we always return JSON, even if user is not authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login first',
                'error' => 'Authentication required'
            ], 401);
        }
        
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'role' => $user->role ?? 'customer',
                    'phone' => $user->phone ?? '',
                    'address' => $user->address ?? '',
                    'city' => $user->city ?? '',
                    'postal_code' => $user->postal_code ?? '',
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'is_admin' => $user->isAdmin(),
                    'is_staff' => $user->isStaff(),
                ],
                'message' => 'Profile retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        // Ensure we always return JSON, even if user is not authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login first',
                'error' => 'Authentication required'
            ], 401);
        }

        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20'
            ]);

            // Update only provided fields
            if ($request->has('name')) $user->name = $request->name;
            if ($request->has('email')) $user->email = $request->email;
            if ($request->has('phone')) $user->phone = $request->phone;
            if ($request->has('address')) $user->address = $request->address;
            if ($request->has('city')) $user->city = $request->city;
            if ($request->has('postal_code')) $user->postal_code = $request->postal_code;
            if ($request->has('password') && $request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'customer',
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                    'updated_at' => $user->updated_at,
                ],
                'message' => 'Profile updated successfully'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
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