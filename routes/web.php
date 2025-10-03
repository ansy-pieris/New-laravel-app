<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\GoogleOAuthController;
use App\Livewire\Shop\CheckoutPage;

/*
|--------------------------------------------------------------------------
| Public Pages (no login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Sanctum Authentication Test Page
Route::get('/sanctum-test', function () {
    return view('sanctum-test');
})->name('sanctum.test');

// Image Display Test Page
Route::get('/image-test', function () {
    return view('image-test');
})->name('image.test');

// Featured Products Styling Test Page
Route::get('/featured-test', function () {
    return view('featured-test');
})->name('featured.test');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Google OAuth Routes (for setting up email integration)
Route::get('/google/gmail/auth', [GoogleOAuthController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google/gmail/callback', [GoogleOAuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/google/gmail/test', [GoogleOAuthController::class, 'testEmail'])->name('google.test');

/*
|--------------------------------------------------------------------------
| Products (Public Browsing)
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Post-login Redirect
|--------------------------------------------------------------------------
| Jetstream/Fortify sends users to /dashboard after login.
| Admin/Staff go to their dashboards; Customers go to Home.
*/
Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes (all authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Customer (login required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Temporarily disabled role middleware: 'role:customer'
    // If you don't want a customer dashboard, keep it removed:
    // Route::view('/customer', 'customer.dashboard')->name('customer.dashboard');

    // Order tracking routes
    Route::get('/track-orders', [OrderTrackingController::class, 'index'])->name('orders.track');
    Route::get('/track-orders/{order}', [OrderTrackingController::class, 'show'])->name('orders.show');
    Route::post('/track-orders/{order}/update-status', [OrderTrackingController::class, 'updateStatus'])->name('orders.update-status');
    
    Route::get('/cart', function() {
        return view('cart.index');
    })->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/debug', function() {
        $items = App\Models\CartItem::with('product')->get();
        return response()->json([
            'total_items' => $items->count(),
            'items' => $items->toArray()
        ]);
    })->name('cart.debug');
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    // add POST routes for checkout, cart updates, etc.
});

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    // Temporarily disabled role middleware: 'role:staff'
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Products Management (shared with admin)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
    Route::post('/products/update', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/products/destroy', [AdminProductController::class, 'destroy'])->name('products.destroy');
    
    // Orders Management (shared with admin)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::post('/orders/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Temporarily disabled role middleware: 'role:admin'
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Staff Management
    Route::get('/manage-staff', [AdminStaffController::class, 'index'])->name('staff');
    Route::post('/staff/store', [AdminStaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/edit/{id}', [AdminStaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/update/{id}', [AdminStaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/delete/{id}', [AdminStaffController::class, 'destroy'])->name('staff.destroy');
    Route::get('/staff/reset-password/{id}', [AdminStaffController::class, 'showResetPasswordForm'])->name('staff.reset-password-form');
    Route::post('/staff/reset-password/{id}', [AdminStaffController::class, 'resetPassword'])->name('staff.reset-password');
    
    // Products Management
    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
    Route::post('/products/update', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/products/destroy', [AdminProductController::class, 'destroy'])->name('products.destroy');
    
    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::post('/orders/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});
