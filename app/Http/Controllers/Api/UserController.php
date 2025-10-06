<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get specific user details by ID
     * Public endpoint for Flutter app consumption
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Return user data without sensitive information
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'message' => 'User retrieved successfully'
        ]);
    }

    /**
     * Get all users (optional - for admin purposes)
     * Can be used for user listings in Flutter admin panels
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $users = User::select('id', 'name', 'email', 'role', 'phone', 'city', 'created_at')
                    ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully'
        ]);
    }
}