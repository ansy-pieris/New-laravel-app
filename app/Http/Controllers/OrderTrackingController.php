<?php

// app/Http/Controllers/OrderTrackingController.php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role ?? null;  // or use $user->hasAnyRole('admin','staff') if using spatie

        $canManage = in_array($role, ['admin','staff']); // adjust if needed

        $orders = Order::with('user')
            ->when(!$canManage, fn($q) => $q->where('user_id', $user->id))
            ->latest('created_at')
            ->paginate(15);

        return view('orders.index', compact('orders', 'canManage'));
    }

    public function show(Order $order)
    {
        $user = auth()->user();
        $role = $user->role ?? null;
        $canManage = in_array($role, ['admin','staff']);

        // If not admin/staff, make sure user can only see their own orders
        if (!$canManage && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['user', 'items.product']);

        return view('orders.show', compact('order', 'canManage'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Gate: only admin/staff (middleware already does this)
        $data = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('success', "Order #{$order->order_id} updated to {$data['status']}.");
    }
}
