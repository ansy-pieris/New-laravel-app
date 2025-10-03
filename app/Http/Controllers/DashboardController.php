<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $role = auth()->user()->role ?? 'customer';

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            default => redirect()->route('home'), // customers -> Home
        };
    }
    
    public function customer()
    {
        $user = Auth::user();
        
        // Redirect non-customers
        if (!$user->isCustomer()) {
            return redirect()->route('dashboard');
        }
        
        return view('dashboard.customer');
    }
    
    public function staff()
    {
        $user = Auth::user();
        
        // Redirect non-staff
        if (!$user->isStaff()) {
            return redirect()->route('dashboard');
        }
        
        return view('dashboard.staff');
    }
    
    public function admin()
    {
        $user = Auth::user();
        
        // Redirect non-admin
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        return view('dashboard.admin');
    }
}