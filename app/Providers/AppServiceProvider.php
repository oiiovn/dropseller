<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request)
    {
        Paginator::useBootstrap();
        // Chia sẻ biến $shop_get cho view `index`
        View::composer('product.report', function ($view) {
            $shop_get = Shop::select('shop_id', 'shop_name')->get()->toArray();

            $view->with('shop_get', $shop_get);
        });
        View::composer('header', function ($view) {
            $user = Auth::user();
            $balace = $user->balance;
            if ($user) {
                $userCode = $user->referral_code;
                $transactions = Transaction::where('description', 'LIKE', "%$userCode%")->get();
                $Transactions_Drop = Transaction::with('order')
                    ->where('description', 'LIKE', "%$userCode%")
                    ->where('bank', 'DROP')
                    ->whereHas('order', function ($query) {
                        $query->where('reconciled', 1); // Lọc các đơn hàng có reconciled = 1
                    })
                    ->get();
                foreach ($Transactions_Drop as $transaction) {
                    $balace += $transaction->amount;
                }

                $totalAmount = 0;

                foreach ($transactions as $transaction) {
                    if ($transaction->type == 'IN') {
                        $totalAmount += $transaction->amount;
                    } elseif ($transaction->type == 'OUT') {
                        $totalAmount -= $transaction->amount;
                    }
                }
                $user->total_amount = $totalAmount;
                $user->save();
                $view->with([
                    'user' => $user,
                    'totalAmount' => $totalAmount,
                    'balace' => $balace
                ]);
            }
        });
        View::composer('index', function ($view) {
            $excludedCodes = ['QUA_TRANG', 'QUA001'];
            $startDate = request()->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d 00:00:00'));
            $endDate = request()->input('end_date', Carbon::now()->format('Y-m-d 23:59:59'));

            $Products = OrderDetail::select(
                'sku',
                DB::raw('MAX(product_name) as product_name'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(image) as image'),
                DB::raw('MAX(shop_id) as shop_id'),
                DB::raw('MAX(unit_cost) as unit_cost'),
                DB::raw('SUM(total_cost) as total_revenue'),
                DB::raw('GROUP_CONCAT(order_id) as order_ids'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
                ->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]) // Lọc theo ngày
                ->whereNotIn('sku', $excludedCodes)
                ->groupBy('sku')
                ->orderByDesc('total_quantity')
                ->take(5)
                ->get();

            // Lấy tổng số tiền của các đơn hàng đã thanh toán
            $totalBillPaid = Order::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
                ->where('payment_status', 'paid') // Chỉ lấy đơn hàng đã thanh toán
                ->sum('total_bill'); // Tổng tiền của đơn hàng

            $view->with([
                'Products' => $Products,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalBillPaid' => $totalBillPaid // Gửi tổng tiền về view
            ]);
        });
    }
}
