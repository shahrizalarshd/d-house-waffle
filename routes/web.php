<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/home');
    }
    return view('welcome');
})->name('welcome');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Payment webhooks (no auth required)
Route::post('/webhook/billplz', [PaymentWebhookController::class, 'billplz'])->name('webhook.billplz');
Route::post('/webhook/toyyibpay', [PaymentWebhookController::class, 'toyyibpay'])->name('webhook.toyyibpay');

// Authenticated routes
Route::middleware('auth')->group(function () {
    
    // Buyer routes (all authenticated users can access)
    Route::get('/home', [BuyerController::class, 'home'])->name('home');
    Route::get('/products', [BuyerController::class, 'products'])->name('products');
    Route::get('/cart', [BuyerController::class, 'cart'])->name('cart');
    Route::get('/orders', [BuyerController::class, 'orders'])->name('buyer.orders');
    Route::get('/orders/{id}', [BuyerController::class, 'orderDetail'])->name('buyer.order.detail');
    Route::get('/profile', [BuyerController::class, 'profile'])->name('buyer.profile');
    Route::put('/profile', [BuyerController::class, 'updateProfile'])->name('buyer.profile.update');
    
    // Checkout and order placement
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');
    Route::get('/payment/{id}', [OrderController::class, 'showPayment'])->name('payment.show');
    
    // QR Payment routes
    Route::get('/orders/{id}/qr-payment', [OrderController::class, 'showQRPayment'])->name('orders.qr-payment');
    Route::post('/orders/{id}/upload-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.upload-proof');
    
    // Seller application routes removed - D'house Waffle is single seller
    
    // Staff routes (basic operations only)
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
        Route::post('/orders/{id}/status', [SellerController::class, 'updateOrderStatus'])->name('orders.status');
        Route::post('/orders/{id}/mark-paid', [SellerController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::post('/orders/{id}/verify-qr', [SellerController::class, 'verifyQrPayment'])->name('orders.verify-qr');
    });
    
    // Owner routes (full business management)
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
        Route::post('/orders/{id}/status', [SellerController::class, 'updateOrderStatus'])->name('orders.status');
        Route::post('/orders/{id}/mark-paid', [SellerController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::post('/orders/{id}/verify-qr', [SellerController::class, 'verifyQrPayment'])->name('orders.verify-qr');
        Route::get('/products', [SellerController::class, 'products'])->name('products');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{id}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        
        // Sales Report & Export
        Route::get('/sales-report', [SellerController::class, 'salesReport'])->name('sales-report');
        Route::get('/sales-report/export', [SellerController::class, 'exportSalesReport'])->name('sales-report.export');
        
        // Business settings (merged from admin)
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/all-orders', [AdminController::class, 'orders'])->name('all-orders');
        
        // QR setup
        Route::get('/profile', [SellerController::class, 'profile'])->name('profile');
        Route::put('/profile', [SellerController::class, 'updateProfile'])->name('profile.update');
    });
    
    // Backward compatibility routes (redirect to new structure)
    Route::middleware('role:staff,owner')->group(function () {
        Route::get('/seller/dashboard', function() {
            return auth()->user()->isOwner() 
                ? redirect()->route('owner.dashboard')
                : redirect()->route('staff.dashboard');
        });
    });
    
    // Super Admin routes
    Route::middleware('role:super_admin')->prefix('super')->name('super.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [\App\Http\Controllers\SuperAdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\SuperAdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/settings/test-billplz', [\App\Http\Controllers\SuperAdminController::class, 'testBillplzConnection'])->name('settings.test-billplz');
        Route::get('/apartments', [\App\Http\Controllers\SuperAdminController::class, 'apartments'])->name('apartments');
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('users');
    });
});
