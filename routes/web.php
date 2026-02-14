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
use App\Http\Controllers\Admin\AdminController;
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
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\PhoneCheckController;
use App\Http\Controllers\Debt\DebtController;
use App\Http\Controllers\Debt\DebtAdminController;


Route::get('/', function () {
    return redirect('/login');
});

// Public route for gift card printing - no authentication required
Route::get('/in-giay-tang-qua', [GiftCardController::class, 'inGiayTangQua'])->name('gift-card.print');
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
    Route::get('/api/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'getDashboardStats']);
    Route::get('/api/chart', [\App\Http\Controllers\Admin\DashboardController::class, 'getChartData']);
    Route::get('/api/chart-v1', [\App\Http\Controllers\Admin\DashboardController::class, 'Chart_v1']);

    Route::get('naptien', [PaymentController::class, 'Getnaptien'])->name('naptien');
    Route::middleware('check_balance')->group(function () {

        Route::get('/admin/generate-balance/{userId}', [AdminController::class, 'generateBalanceHistory']);

        Route::get('order', [ShopController::class, 'Overdue_Order'])->name('Overdue_Order');
        Route::get('/transaction', [TransactionController::class, 'fetchTransactionHistory'])->name('transaction');
        Route::post('/GetUser', [UserController::class, 'GetUser'])->name('GetUser');
        Route::get('/portfolio', [ProfileController::class, 'viewProfile'])->name('portfolio');
        Route::get('/export-orders', [OrderController::class, 'exportOrders']);
        Route::post('/import-order-tiktok', [OrderController::class, 'importOrders']);




        Route::middleware('checkrole')->group(function () {

            //thu chi

        });

        Route::get('/lish', [ProductController::class, 'lish'])->name('productsss');

        Route::post('/order', [OrderController::class, 'order'])->name('order.im');
        Route::get('/update-reconciled', [TransactionController::class, 'updateOrderReconciled'])->name('update.reconciled');
        Route::get('/top-products', [ProductController::class, 'Get_product_top'])->name('products.top');
        Route::get('/chien-dich', [CampaignController::class, 'campaign'])->name('campaign');
        Route::get('/payment', [PaymentController::class, 'thanhtoan'])->name('payment');
        Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
        Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');





        Route::get('/bao-cao-quyet-toan', [SettlementController::class, 'settlementReport'])->name('settlement.settlement-report');
        Route::get('/settlementt', [SettlementController::class, 'showDetail'])->name('settlement.settlement-detail');
    });
    // Thêm route keep-alive
    Route::get('/keep-alive', function () {
        if (!auth()->check()) {
            return response()->json(['redirect' => route('login')], 401);
        }
        return response()->json([], 204); // Return empty response with 204 No Content
    })->name('keep-alive');

    // Hệ thống quản lý nợ - Chủ nợ (tài khoản có personal_code)
    Route::prefix('debt')->name('debt.')->group(function () {
        Route::get('code-verify', [DebtController::class, 'showCodeVerify'])->name('code.verify');
        Route::post('code-verify', [DebtController::class, 'verifyCode'])->name('code.verify.post');
        Route::middleware('debt.code.verified')->group(function () {
            Route::get('dashboard', [DebtController::class, 'dashboard'])->name('dashboard');
            Route::get('thong-bao-tai-cau-truc', [DebtController::class, 'showNoticeRestructuring'])->name('notice-restructuring');
            Route::get('tai-khoan', [DebtController::class, 'showAccountForm'])->name('account');
            Route::put('tai-khoan', [DebtController::class, 'updateAccount'])->name('account.update');
        });
    });

    // Hệ thống quản lý nợ - Con nợ / Admin (tài khoản không có personal_code)
    Route::prefix('quan-ly-no')->name('debt.admin.')->middleware('debt.debtor')->group(function () {
        Route::get('debug-auth', function () {
            return response()->json([
                'auth_id' => auth()->id(),
                'email' => auth()->user()?->email,
                'creditor_1_debtor_user_id' => \App\Models\DebtCreditor::find(1)?->debtor_user_id,
            ]);
        })->name('debug-auth');
        Route::get('/', [DebtAdminController::class, 'index'])->name('index');
        Route::get('tong-no', [DebtAdminController::class, 'summary'])->name('summary');
        Route::get('chu-no/tao', [DebtAdminController::class, 'createCreditor'])->name('creditor.create');
        Route::post('chu-no', [DebtAdminController::class, 'storeCreditor'])->name('creditor.store');
        Route::get('chu-no/{creditor}/sua', [DebtAdminController::class, 'editCreditor'])->name('creditor.edit');
        Route::put('chu-no/{creditor}', [DebtAdminController::class, 'updateCreditor'])->name('creditor.update');
        Route::delete('chu-no/{creditor}', [DebtAdminController::class, 'destroyCreditor'])->name('creditor.destroy');
        Route::get('chu-no/{creditor}/ke-hoach-tra', [DebtAdminController::class, 'repaymentPlans'])->name('repayment-plans');
        Route::post('chu-no/{creditor}/ke-hoach-tra', [DebtAdminController::class, 'storeRepaymentPlan'])->name('repayment-plan.store');
        Route::get('thu-nhap-thang', [DebtAdminController::class, 'monthlyIncomes'])->name('monthly-incomes');
        Route::get('thu-nhap-thang/tao', [DebtAdminController::class, 'createMonthlyIncome'])->name('monthly-income.create');
        Route::get('thu-nhap-thang/tao-60-thang', [DebtAdminController::class, 'createBulk60Months'])->name('monthly-income.create-bulk-60');
        Route::post('thu-nhap-thang/tao-60-thang', [DebtAdminController::class, 'storeBulk60Months'])->name('monthly-income.store-bulk-60');
        Route::post('thu-nhap-thang', [DebtAdminController::class, 'storeMonthlyIncome'])->name('monthly-income.store');
        Route::get('thu-nhap-thang/{income}/sua', [DebtAdminController::class, 'editMonthlyIncome'])->name('monthly-income.edit');
        Route::put('thu-nhap-thang/{income}', [DebtAdminController::class, 'updateMonthlyIncome'])->name('monthly-income.update');
        Route::post('thu-nhap-thang/phan-bo-hang-loat', [DebtAdminController::class, 'distributeBulk'])->name('monthly-income.distribute-bulk');
        Route::post('thu-nhap-thang/{income}/phan-bo', [DebtAdminController::class, 'distributeMonthlyIncome'])->name('monthly-income.distribute');
        Route::post('thu-nhap-thang/{income}/xoa-phan-bo', [DebtAdminController::class, 'deleteDistributions'])->name('monthly-income.delete-distributions');
        Route::post('thu-nhap-thang/xoa-phan-bo-hang-loat', [DebtAdminController::class, 'deleteDistributionsBulk'])->name('monthly-income.delete-distributions-bulk');
        Route::get('phan-bo', [DebtAdminController::class, 'distributions'])->name('distributions');
        Route::get('lich-su-thanh-toan-no', [DebtAdminController::class, 'paymentHistory'])->name('payment-history');
        Route::get('lich-su-ngan-hang-pay2s', [DebtAdminController::class, 'bankHistoryPay2s'])->name('bank-history-pay2s');
        Route::post('phan-bo/{distribution}/da-thanh-toan', [DebtAdminController::class, 'markPaid'])->name('mark-paid');
    });

    //Affiliate mời nhà bán hàng
    Route::get('affiliate', [ProgramController::class, 'affiliatePage'])->name('affiliate.affiliate');
});

// Seller and Product Manager routes
Route::middleware(['auth', 'checkrole:seller,product_manager'])->group(function () {
    Route::get('order_si', [OrderController::class, 'order_si'])->name('order_si');
    Route::post('/program-shop/create', [ProgramController::class, 'createProgramShop'])->name('program.shop.register');
    Route::get('/settlement', [SettlementController::class, 'monthly'])->name('settlement.monthly');
    Route::get('/quang-cao_shop', [ADSController::class, 'ads_shop'])->name('quang_cao_shop');
    Route::get('/balance_history', [BalanceHistoryController::class, 'index'])
        ->name('balance.history');
    Route::get('/dang-san-pham', [ProgramController::class, 'list_program'])->name('list_program');
});

// Admin, manager and product_manager routes
Route::middleware(['auth', 'checkrole:admin,manager,product_manager'])->group(function () {
    Route::post('kiem-tra-bien-dong', [AdminController::class, 'check_AI'])->name('check_AI');
    Route::get('/orders/all', [OrderController::class, 'Get_orders_all'])->name('orders.all');
    Route::get('/orders/data', [OrderController::class, 'getOrdersData'])->name('orders.data');
    Route::get('/api/orders/{id}/details', [OrderController::class, 'getOrderDetails'])->name('orders.details');
    Route::post('/get_shop', [ProductController::class, 'Getshopid'])->name('get_shop');
    Route::get('/program/processing', [OrderController::class, 'Program_processing'])->name('program.processing');
    Route::post('/shops/import', [ShopController::class, 'import'])->name('shops.import');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/products/sync-salework', [ProductController::class, 'syncFromSalework'])->name('products.sync');
    Route::get('/shops_insert', [ShopController::class, 'shop_one'])->name('shops');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');
    Route::get('/shopss', [ShopController::class, 'shops'])->name('shop');
    Route::get('/tat-ca-giao-dich', [TransactionController::class, 'Get_transaction_all'])->name('transaction_all');
    Route::get('/tat-ca-don-hang', [OrderController::class, 'Get_orders_all'])->name('Get_orders_all');
    Route::get('/don-hang/data', [OrderController::class, 'getOrdersData'])->name('orders.data.tat-ca');

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
    Route::post('/balance-issues/delete-all', [BalanceIssueController::class, 'delete_all'])->name('admin.balance_issues.delete_all');
    Route::post('/tao-thanh-toan', [OrderController::class, 'taoThanhToan'])->name('order.taoThanhToan');
    
    // hoàn dơn
    Route::get('/import-don-hoan', [OrderController::class, 'showImportForm'])->name('order.import_don_hoan');
    Route::post('/import-don-hoan', [OrderController::class, 'import']);
    Route::get('/quyet-toan-drop', [UserMonthlyReportController::class, 'index'])->name('user-monthly-reports.index');
    Route::put('/user-monthly-reports/{userMonthlyReport}', [UserMonthlyReportController::class, 'update'])->name('user-monthly-reports.update');
    Route::post('/chay-thanh-toan-quyet-toan', [UserMonthlyReportController::class, 'processPayment'])->name('user-monthly-reports.process-payment');
    
    // Quản lý đơn hoàn
    Route::get('/quan-ly-don-hoan', [OrderController::class, 'allReturnOrders'])->name('return-orders.index');
    Route::post('/thanh-toan-don-hoan/{returnOrder}', [OrderController::class, 'payReturnOrder'])->name('return-orders.pay');
    Route::post('/thanh-toan-tat-ca-don-hoan', [OrderController::class, 'payAllReturnOrders'])->name('return-orders.pay-all');

    Route::get('/Khach_hang', [ProfileController::class, 'Get_all'])->name('Get_all');
    // Other shared admin/manager routes...
});

// Route để làm mới CAPTCHA
Route::get('/reload-captcha', function () {
    return response()->json(['captcha' => \Mews\Captcha\Facades\Captcha::img('default')]);
})->name('reload.captcha');

// Thêm route để kiểm tra tình trạng phiên
Route::get('/check-session', function () {
    if (auth()->check()) {
        return response()->json(['authenticated' => true]);
    }
    return response()->json(['authenticated' => false], 401);
})->middleware('web');

// Route xác thực mã truy cập admin
Route::post('/admin/verify-access', 'App\Http\Controllers\AdminAccessController@verifyAccess')->name('admin.verify_access');

// Routes cho admin
Route::middleware(['auth', 'admin.verified'])->prefix('admin')->group(function () {
    // Trang dashboard admin
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Thêm các route admin khác ở đây
});

// Route group cho admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard admin
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // API lấy dữ liệu dashboard
    // Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

    // // Quản lý người dùng
    // Route::resource('users', AdminUserController::class);

    // // Quản lý shop
    // Route::resource('shops', AdminShopController::class);

    // // Quản lý giao dịch
    // Route::resource('transactions', AdminTransactionController::class);

    // // Quản lý sản phẩm
    // Route::resource('products', AdminProductController::class);

    // // Báo cáo và thống kê
    // Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    // // Quản lý thông báo
    // Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');

    // // Kiểm tra an toàn
    // Route::get('/security', [SecurityController::class, 'index'])->name('security');

    // // Cài đặt hệ thống
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings');

    // // Hoạt động của người dùng
    // Route::get('/activities', [ActivityController::class, 'index'])->name('activities');
});

// Quản lý nhặt hàng - Admin và van.btd90@gmail.com
Route::middleware(['auth', 'pick_order.access'])->group(function () {
    Route::get('/nhat-hang', [\App\Http\Controllers\Admin\PickOrderController::class, 'index'])->name('admin.pick_order.index');
    Route::get('/nhat-hang/pick-orders', [\App\Http\Controllers\Admin\PickOrderController::class, 'getPickOrders'])->name('admin.pick_order.pick_orders');
    Route::get('/nhat-hang/purchase-orders', [\App\Http\Controllers\Admin\PickOrderController::class, 'getPurchaseOrders'])->name('admin.pick_order.purchase_orders');
    Route::post('/nhat-hang/upload-excel', [\App\Http\Controllers\Admin\PickOrderController::class, 'uploadExcel'])->name('admin.pick_order.upload_excel');
    Route::post('/nhat-hang/upload-salework', [\App\Http\Controllers\Admin\PickOrderController::class, 'uploadSalework'])->name('admin.pick_order.upload_salework');
    Route::post('/nhat-hang/mark-picked/{id}', [\App\Http\Controllers\Admin\PickOrderController::class, 'markAsPicked'])->name('admin.pick_order.mark_picked');
    Route::post('/nhat-hang/update-quantity/{id}', [\App\Http\Controllers\Admin\PickOrderController::class, 'updateQuantity'])->name('admin.pick_order.update_quantity');
});

// Routes cho các công cụ check số điện thoại
Route::get('/kiem-tra-so-dien-thoai', [PhoneCheckController::class, 'form'])->name('check_so_dt');
Route::post('/kiem-tra-so-dien-thoai', [PhoneCheckController::class, 'check'])->name('check_so_dt_submit');
Route::post('/gui-username', [PhoneCheckController::class, 'submitUsername'])->name('username.submit');
