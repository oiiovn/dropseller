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
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ADSController;
use App\Http\Controllers\ProgramController;

Route::get('/', function () {
    return view('auth.login');
});

// Nhóm tất cả các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');


    Route::get('order', [ShopController::class, 'Overdue_Order'])->name('Overdue_Order');
    Route::get('order_si', [OrderController::class, 'order_si'])->name('order_si');

    Route::get('naptien', [PaymentController::class, 'Getnaptien'])->name('naptien');
    Route::get('/transaction', [TransactionController::class, 'fetchTransactionHistory'])->name('transaction');

    Route::post('/GetUser', [UserController::class, 'GetUser'])->name('GetUser');
    Route::get('/Khach_hang', [ProfileController::class, 'Get_all'])->name('Get_all');
    Route::get('/portfolio', [ProfileController::class, 'viewProfile'])->name('portfolio');
    Route::get('/export-orders', [OrderController::class, 'exportOrders']);
    Route::post('/import-order-tiktok', [OrderController::class, 'importOrders']);


    Route::post('/shops/import', [ShopController::class, 'import'])->name('shops.import');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/shops_insert', [ShopController::class, 'shop_one'])->name('shops');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');

    Route::middleware('checkrole')->group(function () {
        Route::get('/shopss', [ShopController::class, 'shops'])->name('shop');
        Route::get('/tat-ca-giao-dich', [TransactionController::class, 'Get_transaction_all'])->name('transaction_all');
        Route::get('/tat-ca-don-hang', [OrderController::class, 'Get_orders_all'])->name('Get_orders_all');
        Route::get('list_products', [ProductController::class, 'Getproduct'])->name('list_products');
        Route::get('chuon_trình_san_pham', [ProgramController::class, 'program'])->name('program_view');
        Route::post('/product-report', [ProductController::class, 'fetchProductReport'])->name('product.report');
        Route::get('/get-product/{sku}', [ProgramController::class, 'push_product'])->where('sku', '.*');
        Route::post('/program/store', [ProgramController::class, 'store'])->name('program.store');   // Xử lý lưu dữ liệu
        Route::get('/program-list', [ProgramController::class, 'Program_processing'])->name('procerssing.list');

        Route::get('/quang-cao', [ADSController::class, 'ADS'])->name('quang-cao');
        Route::post('/them-quang-cao', [ADSController::class, 'store'])->name('add.ads');
        Route::get('/quang-cao_all', [ADSController::class, 'ads_all'])->name('quang_cao_all');
        Route::get('/naptien-khach-hang', [TransactionController::class, 'show'])->name('naptien_khach_hang');
        Route::post('/addTransaction', [TransactionController::class, 'addTransaction'])->name('transaction.store');
    });
    Route::get('/quang-cao_shop', [ADSController::class, 'ads_shop'])->name('quang_cao_shop');
    Route::get('/lish', [ProductController::class, 'lish'])->name('productsss');
    Route::post('/get_shop', [ProductController::class, 'Getshopid'])->name('get_shop');
    Route::post('/order', [OrderController::class, 'order'])->name('order.im');
    Route::get('/update-reconciled', [TransactionController::class, 'updateOrderReconciled'])->name('update.reconciled');
    Route::get('/top-products', [ProductController::class, 'Get_product_top'])->name('products.top');
    Route::get('/chien-dich', [CampaignController::class, 'campaign'])->name('campaign');
    Route::get('/payment', [PaymentController::class, 'thanhtoan'])->name('payment');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');

    Route::get('/dang-san-pham', [ProgramController::class, 'list_program'])->name('list_program');
    Route::post('/program-shop/create', [ProgramController::class, 'createProgramShop'])->name('program.shop.register');
});
