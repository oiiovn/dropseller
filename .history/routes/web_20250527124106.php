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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ADSController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\BillwebController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\BalanceHistoryController;
use App\Services\ProgramService;
use App\Http\Controllers\Admin\BalanceIssueController;
use App\Http\Controllers\UserMonthlyReportController;
use App\Http\Controllers\FinanceTrackerController;
use App\Models\User;


Route::get('/', function () {
    return redirect('/login');
});
Route::middleware('auth')->group(function () {
  Route::get('/dashboard', function () {
    $user = auth()->user();

    // Điều kiện để show modal chào mừng (chỉ khi không có query)
    $showWelcomeModal = false;
    if (!request()->query()) {
        $programShops = ProgramService::getUnregisteredProgramsForUser($user);
        $showWelcomeModal = !empty($programShops);
    }

    // Điều kiện để show modal cảnh báo số dư âm — luôn kiểm tra
    $hasNegativeBalance = $user->total_amount < 0;

    return view('index', [
        'showWelcomeModal' => $showWelcomeModal,
        'hasNegativeBalance' => $hasNegativeBalance,
    ]);
})->name('dashboard');
 Route::middleware('check_balance')->group(function () {


    Route::get('/admin/generate-balance/{userId}', [AdminController::class, 'generateBalanceHistory']);
    Route::get('/balance_history', [BalanceHistoryController::class, 'index'])
        ->name('balance.history');
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
        Route::get('/programlist', [ProgramController::class, 'Program_processing'])->name('procerssing');
        Route::get('/tat-ca-giao-dịch', [TransactionController::class, 'get_all_transaction'])->name('get_all_transaction.list');
        Route::get('/thanh-toan-si', [TransactionController::class, 'get_SI_transaction'])->name('get_SI_transaction.list');

        Route::get('/quang-cao', [ADSController::class, 'ADS'])->name('quang-cao');
        Route::post('/them-quang-cao', [ADSController::class, 'store'])->name('add.ads');
        Route::get('/quang-cao_all', [ADSController::class, 'ads_all'])->name('quang_cao_all');
        Route::get('/naptien-khach-hang', [TransactionController::class, 'show'])->name('naptien_khach_hang');
        Route::post('/addTransaction', [TransactionController::class, 'addTransaction'])->name('transaction.store');
        Route::post('/programs/{id}/change-status', [ProgramController::class, 'changeStatus_Program'])->name('program.changeStatus');
        Route::get('/phi-web', [BillwebController::class, 'view_total_bill'])->name('view_total_bill');
        Route::post('/export-totalbill', [BillwebController::class, 'exportTotalBill'])->name('export.totalbill');
        Route::get('/balance-issues', [BalanceIssueController::class, 'index'])->name('admin.balance_issues.index');
        Route::post('/tao-thanh-toan', [OrderController::class, 'taoThanhToan'])->name('order.taoThanhToan');
        // hoàn dơn
        Route::get('/import-don-hoan', [OrderController::class, 'showImportForm'])->name('order.import_don_hoan');
        Route::post('/import-don-hoan', [OrderController::class, 'import']);
        Route::get('/quyet-toan-drop', [UserMonthlyReportController::class, 'index'])->name('user-monthly-reports.index');
        Route::put('/user-monthly-reports/{userMonthlyReport}', [UserMonthlyReportController::class, 'update'])->name('user-monthly-reports.update');

        //thu chi

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

    Route::get('/settlement', [SettlementController::class, 'monthly'])->name('settlement.monthly');

    Route::get('/bao-cao-quyet-toan', [SettlementController::class, 'settlementReport'])->name('settlement.settlement-report');
    Route::get('/settlementt', [SettlementController::class, 'showDetail'])->name('settlement.settlement-detail');

    //thu chi
    Route::get('/finance-tracker', [FinanceTrackerController::class, 'create'])->name('finance_tracker.create');
    Route::post('/finance-tracker/ai-suggest', [\App\Http\Controllers\FinanceTrackerController::class, 'aiSuggest'])->name('finance.ai.suggest');


});
