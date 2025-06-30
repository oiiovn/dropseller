<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard admin với dữ liệu thống kê
     */
    public function index()
    {
        // Lấy dữ liệu tháng hiện tại
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // 1. Đếm người dùng
        $totalUsers = User::count();
        $previousMonthUsers = User::where('created_at', '<', $currentMonth)->count();
        
        // 2. Đếm đơn hàng tháng này
        $monthlyOrders = Order::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
            
        $previousMonthOrders = Order::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
        
        // 3. Tính tổng doanh thu tháng này và tháng trước - đảm bảo dùng cùng một nguồn dữ liệu
        $monthlyRevenue = Order::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_bill');
    
        $previousMonthRevenue = Order::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('total_bill');
        
        // 4. Đếm shop hoạt động - Sửa lại truy vấn để đúng
        $activeShops = Shop::count(); // Đếm tất cả shop
        
        // Đếm shop hoạt động tháng trước (shop đã tạo trước tháng này) - Sửa truy vấn để đúng
        $previousMonthShops = Shop::where('created_at', '<', $currentMonth)->count();
        
        // 5. Lấy dữ liệu biểu đồ doanh thu theo tháng
        $revenueData = $this->getMonthlyRevenue();
        
        // 6. Lấy dữ liệu phân bổ đơn hàng theo trạng thái
        $orderDistribution = $this->getOrderDistribution();
        
        // DEBUG: Log giá trị doanh thu để kiểm tra
        \Illuminate\Support\Facades\Log::debug("Monthly Revenue: $monthlyRevenue, Previous Month Revenue: $previousMonthRevenue");
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'previousMonthUsers',
            'monthlyOrders', 
            'previousMonthOrders',
            'monthlyRevenue', 
            'previousMonthRevenue',
            'activeShops', 
            'previousMonthShops',
            'revenueData',
            'orderDistribution'
        ));
    }
    
    /**
     * Lấy dữ liệu doanh thu theo từng tháng trong năm
     */
    private function getMonthlyRevenue()
    {
        $currentYear = Carbon::now()->year;
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // Doanh thu từ đơn hàng
            $revenue = Order::whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->sum('total_bill');
                
            // Phí dropship theo tháng
            $dropshipFee = Order::whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->sum('total_dropship');
                
            $monthlyData[$month] = [
                'revenue' => $revenue,
                'dropship_fee' => $dropshipFee
            ];
        }
        
        return $monthlyData;
    }
    
    /**
     * Lấy dữ liệu phân bổ đơn hàng theo trạng thái
     */
    private function getOrderDistribution()
    {
        // Sửa truy vấn để sử dụng payment_status thay vì status
        $statuses = Order::select('payment_status', DB::raw('count(*) as total'))
            ->groupBy('payment_status')
            ->get();
        
        // Khởi tạo mảng phân phối mặc định
        $distribution = [
            'Hoàn thành' => 0,
            'Đang xử lý' => 0,
            'Đang vận chuyển' => 0,
            'Đã hủy' => 0
        ];
        
        // Map payment_status từ cơ sở dữ liệu sang các trạng thái hiển thị trên biểu đồ
        foreach ($statuses as $status) {
            switch ($status->payment_status) {
                case 'paid':
                    $distribution['Hoàn thành'] += $status->total;
                    break;
                case 'pending':
                case 'processing':
                    $distribution['Đang xử lý'] += $status->total;
                    break;
                case 'shipping':
                    $distribution['Đang vận chuyển'] += $status->total;
                    break;
                case 'cancelled':
                case 'failed':
                    $distribution['Đã hủy'] += $status->total;
                    break;
                default:
                    // Tất cả trạng thái khác được thêm vào "Đang xử lý"
                    $distribution['Đang xử lý'] += $status->total;
                    break;
            }
        }
        
        return $distribution;
    }

    /**
     * Lấy dữ liệu chart cho trang chủ
     * Mô phỏng theo format tương tự /resources/views/index.blade.php
     */
    private function getHomeStats()
    {
        // Lấy dữ liệu từ DB tương tự như trong file index.blade.php
        $totalBillPaid = Order::where('payment_status', 'paid')
            ->sum('total_bill');
            
        $totalOrders = Order::count();
        
        $totalQuantitySold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->sum('order_items.quantity');
            
        $total_dropship = Order::where('payment_status', 'paid')
            ->sum('dropship_fee');
            
        // Lấy top sản phẩm (tương tự như biến Products trong trang chủ)
        $dateRange = request('date_range', 'Tháng này');
        $startDate = now()->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = now()->endOfDay()->format('Y-m-d H:i:s');
        
        // Điều chỉnh thời gian theo date_range
        switch($dateRange) {
            case 'Hôm nay':
                $startDate = now()->startOfDay()->format('Y-m-d H:i:s');
                $endDate = now()->endOfDay()->format('Y-m-d H:i:s');
                break;
            case 'Hôm qua':
                $startDate = now()->subDay()->startOfDay()->format('Y-m-d H:i:s');
                $endDate = now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
                break;
            case '7 ngày trước':
                $startDate = now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s');
                break;
            case '30 ngày trước':
                $startDate = now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s');
                break;
            case 'Tháng trước':
                $startDate = now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
                $endDate = now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');
                break;
            // Default là tháng này và đã thiết lập ở trên
        }
        
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.sku',
                'products.product_name',
                'products.image',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                'products.unit_cost',
                DB::raw('SUM(order_items.quantity * products.unit_cost) as total_revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.sku', 'products.product_name', 'products.image', 'products.unit_cost')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
            
        return [
            'totalBillPaid' => $totalBillPaid,
            'totalOrders' => $totalOrders,
            'totalQuantitySold' => $totalQuantitySold,
            'total_dropship' => $total_dropship,
            'topProducts' => $topProducts
        ];
    }
    
    /**
     * API endpoint để lấy dữ liệu dashboard cho AJAX
     */
    public function getDashboardData(Request $request)
    {
        // Lấy dữ liệu thống kê
        $stats = $this->getHomeStats();
        $orderDistribution = $this->getOrderDistribution();
        $revenueData = $this->getMonthlyRevenue();
        
        return response()->json([
            'stats' => $stats,
            'orderDistribution' => $orderDistribution,
            'revenueData' => $revenueData
        ]);
    }
}
