<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Shop, Order, OrderDetail};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Auth, Cache};

class DashboardController extends Controller
{
    public function getDashboardStats(Request $request)
    {
        $excludedCodes = ['QUA_TRANG', 'QUA001'];

        // Xử lý range thời gian
        $range = $request->input('range', 'last7days'); // <-- mặc định là 'last7days'
        [$startDate, $endDate] = match ($range) {
            'yesterday'    => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'last7days'    => [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()],
            'lastmonth'    => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'today'        => [Carbon::today()->startOfDay(), Carbon::now()->endOfDay()],
            default        => [
                Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay(),
                Carbon::parse($request->input('end_date', now()))->endOfDay()
            ]
        };

        $productLimit = (int) $request->input('limit', 5);
        $productPage = (int) $request->input('page', 1);
        $shopLimit = (int) $request->input('shop_limit', 5);
        $shopPage = (int) $request->input('shop_page', 1);

        $user = Auth::user();

        $cacheKey = "dashboard_stats_{$user->id}_{$startDate->toDateString()}_{$endDate->toDateString()}_{$productLimit}_{$productPage}_{$shopLimit}_{$shopPage}";
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached);
        }

        // Top sản phẩm
        $products = OrderDetail::select(
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
            ->paginate($productLimit, ['*'], 'page', $productPage)
            ->toArray();

        // Xoá các keys không cần thiết trong pagination
        unset($products['links'], $products['first_page_url'], $products['last_page_url'], $products['next_page_url'], $products['prev_page_url'], $products['path']);

        // Phân quyền
        $role = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->value('roles.slug');

        $isAdmin = in_array($role, ['admin', 'manager']);
        $isSeller = $role === 'seller';

        $totalQuantitySold = 0;
        $totalBillPaid = 0;
        $totalOrders = 0;
        $totalDropship = 0;

        if ($isAdmin) {
            $totalQuantitySold = OrderDetail::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotIn('sku', $excludedCodes)->sum('quantity');

            $totalBillPaid = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_bill');
            $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $totalDropship = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_dropship');
        } elseif ($isSeller) {
            $shopIds = Shop::where('user_id', $user->id)->pluck('shop_id');

            $filterDateClause = "STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?";
            $filterBindings = [$startDate->toDateString(), $endDate->toDateString()];

            $totalQuantitySold = OrderDetail::whereHas('order', function ($q) use ($shopIds, $filterDateClause, $filterBindings) {
                $q->whereIn('shop_id', $shopIds)->whereRaw($filterDateClause, $filterBindings);
            })->whereNotIn('sku', $excludedCodes)->sum('quantity');

            $totalBillPaid = Order::whereIn('shop_id', $shopIds)->whereRaw($filterDateClause, $filterBindings)->sum('total_bill');
            $totalOrders = Order::whereIn('shop_id', $shopIds)->whereRaw($filterDateClause, $filterBindings)->count();
            $totalDropship = Order::whereIn('shop_id', $shopIds)->whereRaw($filterDateClause, $filterBindings)->sum('total_dropship');
        }

        // Đơn theo shop
        $ordersByShop = Order::select(
            'shop_id',
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_bill) as total_revenue')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('shop_id')
            ->orderByDesc('total_revenue')
            ->paginate($shopLimit, ['*'], 'shop_page', $shopPage)
            ->toArray();

        unset($ordersByShop['links'], $ordersByShop['first_page_url'], $ordersByShop['last_page_url'], $ordersByShop['next_page_url'], $ordersByShop['prev_page_url'], $ordersByShop['path']);

        $result = [
            'start_date' => $startDate->toDateTimeString(),
            'end_date' => $endDate->toDateTimeString(),
            'products' => $products,
            'total_quantity_sold' => $totalQuantitySold,
            'total_bill_paid' => $totalBillPaid,
            'total_orders' => $totalOrders,
            'total_dropship' => $totalDropship,
            'total_orders_by_shop' => $ordersByShop,
        ];

        Cache::put($cacheKey, $result, now()->addMinutes(10));
        return response()->json($result);
    }
    public function getChartData(Request $request)
    {
        $range = $request->input('range', 'last7days');

        [$startDate, $endDate] = match ($range) {
            'yesterday' => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'last7days' => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
            'last30days' => [Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay()],
            'lastmonth' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'today'     => [Carbon::today()->startOfDay(), Carbon::now()->endOfDay()],
            default     => [
                Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay(),
                Carbon::parse($request->input('end_date', now()))->endOfDay()
            ]
        };
        $user = Auth::user();
        $shopIds = Shop::where('user_id', $user->id)->pluck('shop_id');
        $raw = Order::selectRaw("DATE(created_at) as date, SUM(total_bill) as total_bill, SUM(total_dropship) as total_dropship")
            ->whereIn('shop_id', $shopIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get();

        $data = [];
        foreach ($raw as $row) {
            $data[$row->date] = [
                'total_bill' => $row->total_bill,
                'total_dropship' => $row->total_dropship,
            ];
        }
        $labels = [];
        $total_Expenses = [];
        $dropships = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy());
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $labels[] = $d;
            $total_Expenses[] = isset($data[$d]) ? (float) $data[$d]['total_bill'] : 0;
            $dropships[] = isset($data[$d]) ? (float) $data[$d]['total_dropship'] : 0;
        }
        $data = [];
        foreach ($labels as $i => $date) {
            $data[] = [
                'date'           => $date,
                'total_expense'  => $total_Expenses[$i],
                'dropship_fee'   => $dropships[$i],
            ];
        }
        return response()->json($data);
    }
}
