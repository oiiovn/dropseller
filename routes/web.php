<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('auth.login');
});

// Nhóm tất cả các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    Route::get('list_products', [ProductController::class, 'Getproduct'])->name('list_products');
    Route::get('order', [OrderController::class, 'Getorder'])->name('order');
    Route::get('order_si', [OrderController::class, 'order_si'])->name('order_si');

    Route::get('naptien', [PaymentController::class, 'Getnaptien'])->name('naptien');
    Route::get('/transaction', [TransactionController::class, 'fetchTransactionHistory'])->name('transaction');

    Route::post('/GetUser', [UserController::class, 'GetUser'])->name('GetUser');
    Route::get('/portfolio', [ProfileController::class, 'viewProfile'])->name('portfolio');
    Route::get('/export-orders', [OrderController::class, 'exportOrders']);
    Route::post('/import-order-tiktok', [OrderController::class, 'importOrders']);


    Route::post('/shops/import', [ShopController::class, 'import'])->name('shops.import');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/shops_insert', [ShopController::class, 'shop_one'])->name('shops');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');
    Route::get('/shopss', [ShopController::class, 'shops'])->name('shop');
    Route::get('/lish', [ProductController::class, 'lish'])->name('productsss');
    Route::post('/product-report', [ProductController::class, 'fetchProductReport'])->name('product.report');
    Route::post('/order', [OrderController::class, 'order'])->name('order.im');

    Route::get('/update-reconciled', [TransactionController::class, 'updateOrderReconciled'])->name('update.reconciled');
    Route::get('/top-products', [ProductController::class, 'Get_product_top'])->name('products.top');
    // Route::post('/get-total-revenue', [HomeController::class, 'data'])->name('getTotalRevenue');
    Route::get('/payment', [PaymentController::class, 'thanhtoan'])->name('payment');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');

    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
});
