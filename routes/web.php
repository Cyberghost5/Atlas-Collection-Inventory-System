<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserLogController;

/*
|--------------------------------------------------------------------------
| Public Customer E-Commerce Storefront Routes (Nigeria Domain)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/', [StorefrontController::class, 'index'])->name('shop.index');
Route::get('/shop', [StorefrontController::class, 'index']);
Route::get('/categories', [StorefrontController::class, 'categories'])->name('shop.categories');
Route::get('/categories/{slug}', [StorefrontController::class, 'categoryShow'])->name('shop.category.show');
Route::get('/shop/product/{slug}', [StorefrontController::class, 'show'])->name('shop.show');
Route::post('/shop/checkout', [StorefrontController::class, 'checkout'])->name('shop.checkout');
Route::get('/order-status/{order_number}', [StorefrontController::class, 'trackOrder'])->name('shop.order-status');
Route::get('/my-orders', [StorefrontController::class, 'myOrders'])->name('shop.my-orders');
Route::get('/receipt/{order_number}', [OrderController::class, 'receipt'])->name('orders.receipt');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Phone Number + Password)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password via Phone
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Back-Office Protection: Staff, Admin & Super Admin Access
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin,staff'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer Orders Processing & Staff Daily Sales Entry
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/admin/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/admin/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/admin/orders/{order_number}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/admin/orders/{order_number}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Customer Directory & Profile Editing (Admin & Staff Access)
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/admin/customers/{phone_or_id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/admin/customers/{phone_or_id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/admin/customers/{phone_or_id}', [CustomerController::class, 'update'])->name('customers.update');

    // Data Export Engines
    Route::get('/admin/export/products', [ExportController::class, 'exportProducts'])->name('export.products');
    Route::get('/admin/export/orders', [ExportController::class, 'exportOrders'])->name('export.orders');
    Route::get('/admin/export/customers', [ExportController::class, 'exportCustomers'])->name('export.customers');

    // Inventory & Products View/Create/Edit & Barcodes
    Route::get('/admin/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::match(['get', 'post', 'delete', 'put'], '/admin/products/barcodes/print', [BarcodeController::class, 'printBulk'])->name('products.barcodes.print');
    Route::get('/admin/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/admin/products/{product}/barcode', [BarcodeController::class, 'show'])->name('products.barcode');
    Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('products.update');

    // Categories & Suppliers Read-Only for Staff
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/admin/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    /*
    |--------------------------------------------------------------------------
    | Admin & Super Admin Exclusive Modules & Actions
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin'])->group(function () {
        // Payment Transactions Ledger (Admin Only)
        Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/admin/transactions/{transaction_number}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/admin/export/transactions', [ExportController::class, 'exportTransactions'])->name('export.transactions');

        // Stock Audit Ledger & Stock Adjustments (Admin Only)
        Route::post('/admin/products/{product}/stock-movement', [StockMovementController::class, 'store'])->name('products.stock-movement.store');
        Route::get('/admin/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

        // Executive Reports & Visual Analytics (Admin Only)
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');

        // User Activity Logs & Staff Action Tracker (Admin Only)
        Route::get('/admin/user-logs', [UserLogController::class, 'index'])->name('user-logs.index');

        // Clear System Caches (Admin Only)
        Route::post('/admin/clear-cache', [DashboardController::class, 'clearCache'])->name('admin.clear-cache');

        // Record Deletions
        Route::delete('/admin/orders/{order_number}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::delete('/admin/customers/{phone_or_id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::delete('/admin/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Category & Supplier Management
        Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::post('/admin/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/admin/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/admin/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Exclusive Module (User Management & Impersonation)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('admin/users', UserController::class, ['names' => 'users']);
        Route::post('/admin/users/{id}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    });
});

Route::middleware(['auth'])->post('/admin/impersonate/stop', [UserController::class, 'stopImpersonating'])->name('impersonate.stop');
