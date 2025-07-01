<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;

class DashboardController extends Controller
{
    public function getDashboardStats(Request $request)
    {
        $excludedCodes = ['QUA_TRANG', 'QUA001'];
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfDay()))->endOfDay();

        $productLimit = (int) $request->input('limit', 5);
        $productPage = (int) $request->input('page', 1);

        $shopLimit = (int) $request->input('shop_limit', 5);
        $shopPage = (int) $request->input('shop_page', 1);

        $user = Auth::user();

        $cacheKey = 'dashboard_stats_' . $user->id
            . '_start_' . $startDate->toDateString()
            . '_end_' . $endDate->toDateString()
            . '_limit_' . $productLimit . '_page_' . $productPage
            . '_shoplimit_' . $shopLimit . '_shoppage_' . $shopPage;

        // Lấy cache nếu có
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

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
            ->paginate($productLimit, ['*'], 'page', $productPage);

        $roleSlug = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->value('roles.slug');

        $isAdminOrManager = in_array($roleSlug, ['admin', 'manager']);
        $isSeller = $roleSlug === 'seller';

        if ($isAdminOrManager) {
            $totalQuantitySold = OrderDetail::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotIn('sku', $excludedCodes)
                ->sum('quantity');

            $totalBillPaid = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_bill');
            $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_dropship');
        } elseif ($isSeller) {
            $userShopIds = Shop::where('user_id', $user->id)->pluck('shop_id');

            $totalQuantitySold = OrderDetail::whereHas('order', function ($query) use ($userShopIds, $startDate, $endDate) {
                $query->whereIn('shop_id', $userShopIds)
                    ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                        $startDate->toDateString(),
                        $endDate->toDateString()
                    ]);
            })
                ->whereNotIn('sku', $excludedCodes)
                ->sum('quantity');

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
            ->paginate($shopLimit, ['*'], 'shop_page', $shopPage);

        $result = [
            'start_date' => $startDate->toDateTimeString(),
            'end_date' => $endDate->toDateTimeString(),
            'products' => $Products->toArray(),
            'total_quantity_sold' => $totalQuantitySold,
            'total_bill_paid' => $totalBillPaid,
            'total_orders' => $totalOrders,
            'total_dropship' => $total_dropship,
            'total_orders_by_shop' => $totalOrdersByShop->toArray(),
        ];

        Cache::put($cacheKey, $result, now()->addMinutes(10));

        return response()->json($result);
    }
}
