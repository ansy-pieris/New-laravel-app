<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for staff dashboard
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        
        // Get logged-in staff member's name
        $userName = auth()->user()->name;

        return view('staff.dashboard', compact(
            'totalCustomers',
            'totalProducts', 
            'totalOrders',
            'userName'
        ));
    }
}