<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\UserController;

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
    Route::get('/export-orders', [OrderController::class, 'exportOrders']);
    Route::post('/import-order-tiktok', [OrderController::class, 'importOrders']);
});
