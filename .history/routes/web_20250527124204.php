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
    Route::middleware('check_balance')->group(function () {});


    //thu chi
    Route::get('/finance-tracker', [FinanceTrackerController::class, 'create'])->name('finance_tracker.create');
    Route::post('/finance-tracker/ai-suggest', [\App\Http\Controllers\FinanceTrackerController::class, 'aiSuggest'])->name('finance.ai.suggest');
});
