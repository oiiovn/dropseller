<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;

use App\Http\Controllers\auth\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/Dashboard', function () {
    return view('index');
})->name('Dashboard');



Route::get('/dashboard', function () {
    return view('index');
})->name('dashboard');
// Sản phẩm
// Sản phẩm
Route::get('list_products', [ProductController::class, 'Getproduct'])->name('list_products');
Route::get('order', [OrderController::class, 'Getorder'])->name('order');

// Payment
// Payment
Route::get('naptien', [PaymentController::class, 'Getnaptien'])->name('naptien');
Route::get('/transaction', [TransactionController::class, 'fetchTransactionHistory'])->name('transaction');
Route::get('/transaction', [TransactionController::class, 'fetchTransactionHistory'])->name('transaction');

// Đăng ký đăng nhập
// Đăng ký đăng nhập
Route::get('/next_page', [AuthController::class, 'next_page'])->name('next_page');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.jwt');
