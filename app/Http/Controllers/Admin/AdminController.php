<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Models\BalanceHistory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\BalanceIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function check_AI()
    {
        $users = User::all();
        $results = [];

        foreach ($users as $user) {
            $userLogs = [];
            $userLogs[] = "🧑 Kiểm tra user ID: {$user->id} ({$user->name})";

            $histories = BalanceHistory::where('user_id', $user->id)
                ->orderBy('id', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($histories->count() < 1) {
                $userLogs[] = "⚠️ Không có lịch sử giao dịch.";
                $results[] = $userLogs;
                continue;
            }

            // ✅ Kiểm tra giao dịch đầu tiên
            $first = $histories->first();
            if (abs($first->balance_after - $first->amount_change) > 0.01) {
                $prompt = "Giao dịch đầu tiên: amount_change = {$first->amount_change}, balance_after = {$first->balance_after}
            → Lẽ ra số dư sau phải bằng số tiền thay đổi. Có sai không?";

                $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Bạn là AI chuyên kiểm tra logic số dư sau giao dịch.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 150,
                ]);

                $content = $response['choices'][0]['message']['content'] ?? 'Không có phản hồi.';
                $userLogs[] = "❌ Lệch ở giao dịch đầu tiên ID {$first->id} → GPT: $content";

                BalanceIssue::create([
                    'user_id' => $user->id,
                    'balance_history_id' => $first->id,
                    'expected_balance' => $first->amount_change,
                    'actual_balance' => $first->balance_after,
                    'message' => $content,
                ]);
            } else {
                $userLogs[] = "✅ Giao dịch đầu tiên ID {$first->id} hợp lệ.";
            }

            // 🔁 Kiểm tra các giao dịch còn lại
            for ($i = 1; $i < $histories->count(); $i++) {
                $prev = $histories[$i - 1];
                $curr = $histories[$i];

                $expected = $prev->balance_after + $curr->amount_change;
                $actual = $curr->balance_after;

                if (abs($expected - $actual) > 0.01) {
                    $prompt = "Giao dịch trước: balance_after = {$prev->balance_after}
                Giao dịch hiện tại: amount_change = {$curr->amount_change}, balance_after = {$actual}
                Tính đúng: {$prev->balance_after} + ({$curr->amount_change}) = {$expected}
                Hệ thống ghi: {$actual}
                → Số dư có đúng không? Đúng/Sai + giải thích ngắn.";

                    $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Bạn là AI chuyên kiểm tra logic số dư sau giao dịch.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 150,
                    ]);

                    $content = $response['choices'][0]['message']['content'] ?? 'Không có phản hồi.';
                    $userLogs[] = "❌ Lệch ở ID {$curr->id} → GPT: $content";

                    BalanceIssue::create([
                        'user_id' => $user->id,
                        'balance_history_id' => $curr->id,
                        'expected_balance' => $expected,
                        'actual_balance' => $actual,
                        'message' => $content,
                    ]);
                } else {
                    $userLogs[] = "✅ ID {$curr->id} hợp lệ.";
                }
            }

            $userLogs[] = "───────────────────────────────";
        }

        return redirect()->back();
    }
    public function check_code()
    {
        // Kiểm tra quyền truy cập
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này');
        }

        // Lấy tất cả các mã từ cơ sở dữ liệu để kiểm tra
        $referralCodes = User::pluck('referral_code')->toArray();
        $duplicateCodes = array_filter(array_count_values($referralCodes), function ($count) {
            return $count > 1;
        });

        $results = [
            'duplicate_codes' => $duplicateCodes,
            'invalid_format' => [],
            'stats' => [
                'total' => count($referralCodes),
                'unique' => count(array_unique($referralCodes)),
                'duplicates' => count($duplicateCodes),
            ]
        ];

        // Kiểm tra định dạng mã
        $users = User::all();
        foreach ($users as $user) {
            $code = $user->referral_code;

            // Kiểm tra định dạng mã (5 ký tự chữ và số viết hoa)
            if ($code && (!preg_match('/^[A-Z0-9]{5}$/', $code))) {
                $results['invalid_format'][] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'code' => $code,
                    'issue' => 'Mã không đúng định dạng (cần 5 ký tự chữ hoa và số)'
                ];
            }
        }

        // Kiểm tra xung đột với các mã giao dịch
        $transactionCodes = Transaction::pluck('transaction_id')->toArray();
        $conflictCodes = array_intersect($referralCodes, $transactionCodes);

        if (!empty($conflictCodes)) {
            $results['transaction_conflicts'] = $conflictCodes;
        }

        // Ghi lại log kết quả kiểm tra
        Log::info('Admin check_code: ' . json_encode($results));

        // Hiển thị kết quả trên giao diện người dùng
        session(['code_check_results' => $results]);

        return view('admin.code_check', [
            'results' => $results,
            'users' => $users
        ]);
    }

    /**
     * Tự động sửa mã không hợp lệ
     */
    public function fix_invalid_codes()
    {
        // Kiểm tra quyền truy cập
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này');
        }

        $users = User::whereRaw("LENGTH(referral_code) != 5 OR referral_code NOT REGEXP '^[A-Z0-9]{5}$'")
            ->orWhereNull('referral_code')
            ->get();

        $fixed = 0;

        foreach ($users as $user) {
            $newCode = $this->generateUniqueReferralCode();
            $user->referral_code = $newCode;
            $user->save();
            $fixed++;

            Log::info("Fixed user ID {$user->id} referral code to: {$newCode}");
        }

        // Sửa mã trùng lặp
        $referralCodes = User::pluck('referral_code', 'id')->toArray();
        $codeCount = array_count_values($referralCodes);

        foreach ($codeCount as $code => $count) {
            if ($count > 1) {
                // Tìm tất cả người dùng có mã trùng lặp
                $duplicateUsers = User::where('referral_code', $code)->get();

                // Giữ nguyên mã cho người dùng đầu tiên, thay đổi cho những người còn lại
                for ($i = 1; $i < count($duplicateUsers); $i++) {
                    $user = $duplicateUsers[$i];
                    $newCode = $this->generateUniqueReferralCode();
                    $user->referral_code = $newCode;
                    $user->save();
                    $fixed++;

                    Log::info("Fixed duplicate code for user ID {$user->id}: {$code} -> {$newCode}");
                }
            }
        }

        return redirect()->route('admin.check_code')->with('success', "Đã sửa {$fixed} mã không hợp lệ");
    }

    /**
     * Tạo mã giới thiệu dựa trên cấu hình
     */
    private function generateUniqueReferralCode(): string
    {
        // Lấy mã tiền tố từ cấu hình (ví dụ: "DS")
        $prefix = config('app.referral_code_prefix', 'DS');

        // Lấy độ dài phần số (mặc định: 3 chữ số)
        $numberLength = config('app.referral_code_number_length', 3);

        // Bắt đầu từ số được cấu hình hoặc mặc định từ 1
        $startNumber = config('app.referral_code_start', 1);

        // Tìm mã cuối cùng đã được sử dụng để tạo mã tiếp theo
        $lastCode = User::where('referral_code', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(referral_code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('referral_code');

        if ($lastCode) {
            // Trích xuất phần số từ mã cuối cùng
            $lastNumber = (int) substr($lastCode, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $startNumber;
        }

        // Tạo mã mới với số được định dạng theo độ dài cấu hình
        $code = $prefix . str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);

        // Kiểm tra xem mã đã tồn tại chưa và tăng số nếu cần
        while (User::where('referral_code', $code)->exists()) {
            $nextNumber++;
            $code = $prefix . str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    /**
     * Xác thực mã truy cập admin
     */
    public function verifyAccessCode(Request $request)
    {
        // Lấy mã truy cập từ config (ở đây mã được đặt cố định trong config)
        $validAccessCode = config('admin.access_code', 'ADMIN_1994');

        $inputCode = $request->input('access_code');

        if ($inputCode === $validAccessCode) {
            // Lưu trạng thái xác thực vào session
            session(['admin_access_verified' => true, 'admin_verified_at' => now()]);

            // Log hoạt động
            Log::info("Admin access code verified by user: " . auth()->user()->id);

            return redirect()->back()->with([
                'access_code_status' => 'success',
                'access_code_message' => 'Mã truy cập hợp lệ. Quyền quản trị cao cấp đã được kích hoạt.'
            ]);
        }

        // Log thất bại
        Log::warning("Failed admin access code attempt by user: " . auth()->user()->id . ", code: " . $inputCode);

        return redirect()->back()->with([
            'access_code_status' => 'error',
            'access_code_message' => 'Mã truy cập không hợp lệ. Vui lòng thử lại.'
        ]);
    }

    /**
     * Kiểm tra xác thực mã truy cập admin (để sử dụng trong middleware)
     */
    public static function isAdminAccessVerified()
    {
        // Kiểm tra xem admin đã xác thực mã truy cập chưa
        if (!session()->has('admin_access_verified') || !session()->has('admin_verified_at')) {
            return false;
        }

        // Kiểm tra xem xác thực có quá cũ không (ví dụ: hết hạn sau 1 giờ)
        $verifiedAt = session('admin_verified_at');
        $expiresAt = Carbon::parse($verifiedAt)->addHour();

        if (now()->greaterThan($expiresAt)) {
            // Xác thực đã hết hạn
            session()->forget(['admin_access_verified', 'admin_verified_at']);
            return false;
        }

        return session('admin_access_verified') === true;
    }















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
