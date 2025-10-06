<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomepageApiController extends Controller
{
    /**
     * Get homepage data for mobile app - simplified version
     */
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => 'Homepage API is working!',
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}