<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Get all orders with user information
        $orders = Order::select(
            'orders.order_id',
            'orders.total_price',
            'orders.status',
            'orders.created_at',
            'users.name as user_name'
        )
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->orderBy('orders.created_at', 'desc')
        ->get();

        // Detect if this is being accessed by staff or admin
        $userRole = Auth::user()->role;
        $routePrefix = $userRole === 'staff' ? 'staff' : 'admin';

        return view('admin.orders', compact('orders', 'routePrefix'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        try {
            $order = Order::where('order_id', $request->order_id)->firstOrFail();
            $order->status = $request->status;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status'
            ], 500);
        }
    }
}