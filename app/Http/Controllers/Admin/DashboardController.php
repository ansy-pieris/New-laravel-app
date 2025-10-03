<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get logged-in admin's name
        $userName = Auth::user()->name ?? 'Admin';

        // Load dashboard statistics
        $totalCustomers = User::where('role', 'customer')->count();
        $totalStaff = User::where('role', 'staff')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        return view('admin.dashboard', compact(
            'userName',
            'totalCustomers',
            'totalStaff',
            'totalProducts',
            'totalOrders'
        ));
    }
}