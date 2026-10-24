<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminRevenueController;
use App\Http\Controllers\Admin\AdminReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes - SMART CASUAL LARAVEL
|--------------------------------------------------------------------------
*/

// Public storefront routes
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/about', [CatalogController::class, 'about'])->name('about');
Route::get('/products/{id}', [CatalogController::class, 'show'])->name('products.detail');
Route::post('/products/{id}/reviews', [CatalogController::class, 'submitReview'])->name('products.reviews.submit');

// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// Customer order routes (Strictly protected by auth:web)
Route::prefix('account/orders')->name('orders.')->middleware('auth:web')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [OrderController::class, 'detail'])->name('detail');
    Route::post('/{id}/confirm-received', [OrderController::class, 'confirmReceived'])->name('confirm-received');
    Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});

// Customer Authentication
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// Admin Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Protected Routes (Staff & Manager)
    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Order Management (Staff & Manager)
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('orders/{id}/payment', [AdminOrderController::class, 'togglePayment'])->name('orders.payment');

        // Categories & Products List (Staff & Manager can view)
        Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('products', [AdminProductController::class, 'index'])->name('products.index');

        // Reviews Moderation (Staff & Manager)
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{id}/visibility', [AdminReviewController::class, 'toggleVisibility'])->name('reviews.visibility');
        Route::delete('reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Manager ONLY Protected Routes (RBAC)
        Route::middleware('manager')->group(function () {
            // Revenue Statistics (Manager only)
            Route::get('revenue', [AdminRevenueController::class, 'index'])->name('revenue.index');

            // Category mutations (Manager only)
            Route::get('categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
            Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
            Route::get('categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

            // Product mutations (Manager only)
            Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::put('products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::delete('products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
            Route::post('products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        });
    });
});
