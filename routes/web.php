<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;

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



Route::get('list_products', [ProductController::class, 'Getproduct'])->name('list_products');
Route::get('naptien', [PaymentController::class, 'Getnaptien'])->name('naptien');