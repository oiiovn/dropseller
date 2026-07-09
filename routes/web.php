<?php

use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
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
use App\Http\Controllers\Crm\DebtController as CrmDebtController;
use App\Http\Controllers\Crm\OrderController as CrmOrderController;
use App\Http\Controllers\Crm\ReportController as CrmReportController;
use App\Http\Controllers\Crm\PaymentController as CrmPaymentController;
use App\Http\Controllers\Crm\ProductController as CrmProductController;
use App\Http\Controllers\Crm\DeliveryController as CrmDeliveryController;
use App\Http\Controllers\Crm\CustomerController as CrmCustomerController;
use App\Http\Controllers\Crm\CrmPageController;
use App\Http\Controllers\Crm\AffiliateController as CrmAffiliateController;
use App\Http\Controllers\Crm\CommissionController as CrmCommissionController;
use App\Http\Controllers\Crm\CommissionWalletController;
use App\Http\Controllers\Crm\CommissionRuleController as CrmCommissionRuleController;
use App\Http\Controllers\Crm\PinController as CrmPinController;
use App\Http\Controllers\Crm\CrmNotificationController;
use App\Http\Controllers\Crm\ProfileController as CrmProfileController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminAppearanceController;

Route::get('/', function () {
    return view('auth.login');
});

// Nhóm tất cả các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect(RouteServiceProvider::homeForUser(auth()->user()));
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

    Route::middleware('checkrole')->group(function () {
        Route::get('/shopss', [ShopController::class, 'shops'])->name('shop');
    });
    
    Route::get('/lish', [ProductController::class, 'lish'])->name('productsss');
    Route::post('/product-report', [ProductController::class, 'fetchProductReport'])->name('product.report');
    Route::post('/order', [OrderController::class, 'order'])->name('order.im');

    Route::get('/update-reconciled', [TransactionController::class, 'updateOrderReconciled'])->name('update.reconciled');
    Route::get('/top-products', [ProductController::class, 'Get_product_top'])->name('products.top');
    // Route::post('/get-total-revenue', [HomeController::class, 'data'])->name('getTotalRevenue');
    Route::get('/payment', [PaymentController::class, 'thanhtoan'])->name('payment');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');

    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');

    Route::get('/crm', CrmPageController::class)->name('crm.home');
    Route::get('/crm/{any}', CrmPageController::class)->where('any', '.*');

    Route::get('/admin', AdminPageController::class)->name('admin.home');
    Route::get('/admin/{any}', AdminPageController::class)->where('any', '.*')->name('admin.any');

    Route::prefix('crm-api')->group(function () {
        Route::get('me', [CrmProfileController::class, 'me']);
        Route::get('me/affiliate', [CrmProfileController::class, 'affiliate']);
        Route::get('dashboard', [CrmReportController::class, 'dashboard']);
        Route::get('reports/advanced', [CrmReportController::class, 'advanced']);
        Route::get('reports/affiliates', [CrmReportController::class, 'affiliatePerformance']);
        Route::get('alerts/overdue-debts', [CrmReportController::class, 'overdueAlerts']);
        Route::get('notifications/unread-count', [CrmNotificationController::class, 'unreadCount']);
        Route::put('notifications/read-all', [CrmNotificationController::class, 'markAllRead']);
        Route::put('notifications/{notification}/read', [CrmNotificationController::class, 'markRead']);
        Route::get('notifications', [CrmNotificationController::class, 'index']);
        Route::get('settings/appearance', [AdminAppearanceController::class, 'show']);
        Route::apiResource('affiliates', CrmAffiliateController::class);
        Route::apiResource('customers', CrmCustomerController::class);
        Route::apiResource('products', CrmProductController::class);
        Route::get('orders/{order}/transitions', [CrmOrderController::class, 'transitions']);
        Route::post('orders/{order}/payments', [CrmOrderController::class, 'storePayment']);
        Route::apiResource('orders', CrmOrderController::class);
        Route::put('payments/{payment}/approve', [CrmPaymentController::class, 'approve']);
        Route::put('payments/{payment}/reject', [CrmPaymentController::class, 'reject']);
        Route::apiResource('payments', CrmPaymentController::class);
        Route::apiResource('debts', CrmDebtController::class);
        Route::apiResource('deliveries', CrmDeliveryController::class);
        Route::apiResource('commissions', CrmCommissionController::class);
        Route::get('commission-settlements', [CommissionWalletController::class, 'listSettlements']);
        Route::get('commission-wallets', [CommissionWalletController::class, 'index']);
        Route::get('commission-wallets/{affiliate}', [CommissionWalletController::class, 'show']);
        Route::post('commission-wallets/{affiliate}/settlements', [CommissionWalletController::class, 'createSettlement']);
        Route::put('commission-settlements/{settlement}/settle', [CommissionWalletController::class, 'settle']);
        Route::apiResource('commission-rules', CrmCommissionRuleController::class);
        Route::apiResource('pins', CrmPinController::class);
        Route::post('commissions/{commission}/payout', [CrmCommissionController::class, 'payout']);
    });

    Route::prefix('admin-api')->middleware('crm.role:admin')->group(function () {
        Route::get('dashboard', AdminDashboardController::class);
        Route::get('users', [AdminUserController::class, 'index']);
        Route::put('users/{user}', [AdminUserController::class, 'update']);
        Route::delete('users/{user}', [AdminUserController::class, 'destroy']);
        Route::post('collaborators', [AdminUserController::class, 'storeCollaborator']);
        Route::get('orders/{order}/transitions', [AdminOrderController::class, 'transitions']);
        Route::get('orders/{order}/audit-trail', [AdminOrderController::class, 'auditTrail']);
        Route::post('orders/{order}/payments', [AdminOrderController::class, 'storePayment']);
        Route::get('payments', [CrmPaymentController::class, 'index']);
        Route::get('commission-settlements', [CommissionWalletController::class, 'listSettlements']);
        Route::put('commission-settlements/{settlement}/settle', [CommissionWalletController::class, 'settle']);
        Route::put('payments/{payment}/approve', [CrmPaymentController::class, 'approve']);
        Route::put('payments/{payment}/reject', [CrmPaymentController::class, 'reject']);
        Route::post('orders/{order}/restore', [AdminOrderController::class, 'restore']);
        Route::apiResource('orders', AdminOrderController::class);
        Route::apiResource('affiliates', CrmAffiliateController::class);
        Route::apiResource('commission-rules', CrmCommissionRuleController::class);
        Route::apiResource('pins', CrmPinController::class);
        Route::get('reports/advanced', [CrmReportController::class, 'advanced']);
        Route::get('reports/affiliates', [CrmReportController::class, 'affiliatePerformance']);
        Route::get('settings/appearance', [AdminAppearanceController::class, 'show']);
        Route::put('settings/appearance', [AdminAppearanceController::class, 'update']);
    });
});
