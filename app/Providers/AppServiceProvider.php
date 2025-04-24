<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Notification;
use App\Observers\TransactionObserver;
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
        Transaction::observe(TransactionObserver::class);
        Carbon::setLocale('vi');
        Paginator::useBootstrap();
        // Chia sẻ biến $shop_get cho view `index`
        View::composer('product.report', function ($view) {
            $shop_get = Shop::select('shop_id', 'shop_name')->get()->toArray();

            $view->with('shop_get', $shop_get);
        });
        View::composer('header', function ($view) {
            $user = Auth::user();
            $balace = $user->balance;
            $totalAmount = $user->total_amount;
            if ($user) {
                $userCode = $user->referral_code;
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
            $startDate = request()->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));          
            $endDate = request()->input('end_date', Carbon::now()->endOfDay());
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
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
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotIn('sku', $excludedCodes)
                ->groupBy('sku')
                ->orderByDesc('total_quantity')
                ->paginate(5);

            // Kiểm tra người dùng có đăng nhập không
            $user = Auth::user();
            if ($user && $user->role == '2') {
                $totalQuantitySold = OrderDetail::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotIn('sku', $excludedCodes)
                    ->sum('quantity');

                $totalBillPaid = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_bill');

                $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_dropship');
            } else {
                // Lấy danh sách shop của user hiện tại
                $userShopIds = Shop::where('user_id', optional($user)->id)->pluck('shop_id');

                $totalQuantitySold = OrderDetail::whereHas('order', function ($query) use ($userShopIds, $startDate, $endDate) {
                    $query->whereIn('shop_id', $userShopIds)
                        ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                            $startDate->toDateString(),
                            $endDate->toDateString()
                        ]);
                })
                    ->whereNotIn('sku', $excludedCodes)
                    ->sum('quantity');
// dd($totalQuantitySold);
                $totalBillPaid = Order::whereIn('shop_id', $userShopIds)
                    ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                        $startDate->toDateString(),
                        $endDate->toDateString()
                    ])
                    ->sum('total_bill');

                $totalOrders = Order::whereIn('shop_id', $userShopIds)
                    ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                        $startDate->toDateString(),
                        $endDate->toDateString()
                    ])
                    ->count();

                $total_dropship = Order::whereIn('shop_id', $userShopIds)
                    ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                        $startDate->toDateString(),
                        $endDate->toDateString()
                    ])
                    ->sum('total_dropship');
            }

            $totalOrdersByShop = Order::select(
                'shop_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_bill) as total_revenue')
            )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('shop_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->get();
            $view->with([
                'Products' => $Products,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalBillPaid' => $totalBillPaid,
                'totalQuantitySold' => $totalQuantitySold,
                'totalOrders' => $totalOrders,
                'total_dropship' => $total_dropship,
                'totalOrdersByShop' => $totalOrdersByShop
            ]);
        });

        View::composer('*', function ($view) {
            $Notifications = Notification::where('user_id', Auth::id())
                ->with('user', 'shop')
                ->orderBy('created_at', 'desc')
                ->get();
            $unreadNotificationsCount = $Notifications->where('is_read', 0)->count();
            $unreadNotifications = $Notifications->where('is_read', 0)->values(); // Dùng `values()` để giữ lại collection hợp lệ
            $NotificationsCount = $Notifications->count();

            $orders_unpaid = Order::where('payment_status', 'Chưa thanh toán')
                ->whereHas('shop', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('created_at', '<', Carbon::now()->subDay())
                ->get();

            $view->with(
                [
                    'orders_unpaid' => $orders_unpaid,
                    'Notifications' => $Notifications,
                    'NotificationsCount' => $NotificationsCount,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                    'unreadNotifications' => $unreadNotifications
                ]
            );
        });
    }
}
