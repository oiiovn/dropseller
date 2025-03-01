<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;

class ProcessOrderCommand extends Command
{
    protected $signature = 'order:process';
    protected $description = 'Tự động xử lý đơn hàng và lọc sản phẩm từ API theo danh sách shop_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('🚀 Bắt đầu lọc sản phẩm và tạo đơn hàng...');
        $shopIds = Shop::pluck('shop_id')->toArray();
        if (empty($shopIds)) {
            Log::error("❌ Không có shop_id nào trong database.");
            return;
        }
        $apiUrl = "https://salework.net/api/open/stock/v1/report/product";
        $clientId = "1605";
        $token = "+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L";

        $today = Carbon::today();
        $firstRunDate = Carbon::createFromFormat('Y-m-d', '2025-03-01');
        $startDate = $today->subDays(20); 
        if ($startDate < $firstRunDate) {
            $startDate = $firstRunDate;
        }

        $endDate = Carbon::today();
        while ($startDate <= $endDate) {
            $timeStart = Carbon::parse($startDate->format('Y-m-d') . ' 00:00:00', 'Asia/Ho_Chi_Minh')->timestamp * 1000;
            $timeEnd = Carbon::parse($startDate->format('Y-m-d') . ' 23:59:59', 'Asia/Ho_Chi_Minh')->timestamp * 1000;

            echo "Start: " . $startDate->toDateString() . " - Time Start: " . $timeStart . "\n";
            echo "End: " . $startDate->toDateString() . " 23:59:59 - Time End: " . $timeEnd . "\n";

            $startDate->addDay();
            $platform = "Tiktok";
            foreach ($shopIds as $shopId) {
                Log::info("🔍 Đang xử lý shop_id: $shopId...");
                $payload = [
                    "time_start" => $timeStart,
                    "time_end" => $timeEnd,
                    "platform" => $platform
                ];
                $response = $this->sendApiRequest($apiUrl, $payload, $clientId, $token);
                if (!$response) {
                    Log::error("⚠️ API trả về dữ liệu rỗng hoặc lỗi kết nối cho shop_id: $shopId.");
                    continue;
                }
                $data = json_decode($response, true);
                if (!isset($data['status']) || $data['status'] !== 'success') {
                    Log::error("❌ API trả về lỗi cho shop_id $shopId: " . ($data['message'] ?? 'Không xác định.'));
                    continue;
                }
                if (!isset($data['data']['product_report'][$platform]) || empty($data['data']['product_report'][$platform])) {
                    Log::warning("⚠️ Không có sản phẩm nào trong báo cáo của shop_id: $shopId.");
                    continue;
                }
                $productReport = $data['data']['product_report'][$platform] ?? [];
                if ($shopId) {
                    $productReport = array_filter($productReport, function ($shop) use ($shopId) {
                        return isset($shop['shopId']) && $shop['shopId'] == $shopId;
                    });
                }
                $filteredProducts = [];
                foreach ($productReport as $shop) {
                    if (isset($shop['products']) && is_array($shop['products'])) {
                        foreach ($shop['products'] as $product) {
                            $productFromDb = Product::where('sku', $product['code'])->first();
                            $filteredProducts[] = [
                                'code' => $product['code'],
                                'name' => $product['name'],
                                'amount' => $product['amount'],
                                'api_price' => $product['revenue'],
                                'image' => $product['image'],
                                'db_price' => $productFromDb ? $productFromDb->price : 'Không tìm thấy',
                            ];
                        }
                    }
                }
                if (empty($filteredProducts)) {
                    Log::info("⚠️ Không có sản phẩm nào được lọc từ API cho shop_id: $shopId. Bỏ qua việc tạo order.");
                    continue;
                }
                $filter_date = Carbon::createFromTimestampMs($timeStart, 'Asia/Ho_Chi_Minh')->format('Y-m-d') .
                    ' - ' .
                    Carbon::createFromTimestampMs($timeEnd, 'Asia/Ho_Chi_Minh')->format('Y-m-d');
                $excludedCodes = ['QUA_TRANG', 'QUA001'];
                $excludedShopIds = ['7495109251985279454', '7495962777620351819','7495178219156178956','7495013968145386053','269548567'];
                $totalAmount = 0;
                $totalRevenue = 0;
                foreach ($filteredProducts as $order) {
                    $totalRevenue += $order['db_price'] * $order['amount'];
                    if (!in_array($order['code'], $excludedCodes)) {
                        $totalAmount += $order['amount'];
                    }
                }

                $total_dropship = in_array($shopId, $excludedShopIds, true) ? 0 : $totalAmount * 5000;
                $total_tong = $totalRevenue + $total_dropship;

                $orderCode = 'DROP' . substr(str_shuffle('0123456789'), 0, 12);
                $totalAmounts = array_sum(array_column($filteredProducts, 'amount'));
                $order = Order::where('filter_date', $filter_date)
                    ->where('shop_id', $shopId)
                    ->first();
                if ($order) {
                    $isSame = (
                        $order->total_products == $totalAmount &&
                        $order->total_dropship == $total_dropship &&
                        $order->total_bill == $total_tong
                    );

                    if ($isSame) {
                        Log::info("✅ Đơn hàng này đã có sẵn!");
                    } else {
                        $total_tong_up = $order->total_dropship + $totalRevenue;
                        $order->update([
                            'total_products' => $totalAmount,
                            'total_bill' => $total_tong_up,
                        ]);
                        $existingSkus = collect($filteredProducts)->pluck('code')->toArray();
                        OrderDetail::where('order_id', $order->id)
                            ->whereNotIn('sku', $existingSkus)
                            ->delete();
                        foreach ($filteredProducts as $detail) {
                            $orderDetail = OrderDetail::where('order_id', $order->id)
                                ->where('sku', $detail['code'])
                                ->first();
                            if ($orderDetail) {
                                $orderDetail->update([
                                    'shop_id' => $order->shop_id,
                                    'sku' => $detail['code'],
                                    'image' => $detail['image'],
                                    'product_name' => $detail['name'],
                                    'quantity' => $detail['amount'],
                                    'unit_cost' => $detail['db_price'],
                                    'total_cost' => $detail['amount'] * $detail['db_price'],
                                ]);
                            } else {
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'shop_id' => $order->shop_id,
                                    'sku' => $detail['code'],
                                    'image' => $detail['image'],
                                    'product_name' => $detail['name'],
                                    'quantity' => $detail['amount'],
                                    'unit_cost' => $detail['db_price'],
                                    'total_cost' => $detail['amount'] * $detail['db_price'],
                                ]);
                            }
                        }
                        Log::info("✅ Đơn hàng đã được cập nhật thành công!");
                    }
                } else {
                    $order = Order::create([
                        'order_code' => $orderCode,
                        'export_date' => now(),
                        'filter_date' => $filter_date,
                        'shop_id' => $shopId,
                        'total_products' => $totalAmount,
                        'total_dropship' => $total_dropship,
                        'total_bill' => $total_tong,
                        'payment_status' => 'Chưa thanh toán',
                        'payment_code' => null,
                    ]);

                    foreach ($filteredProducts as $detail) {
                        $OrderDetail = OrderDetail::create([
                            'order_id' => $order->id,
                            'shop_id' => $order->shop_id,
                            'sku' => $detail['code'],
                            'image' => $detail['image'],
                            'product_name' => $detail['name'],
                            'quantity' => $detail['amount'],
                            'unit_cost' => $detail['db_price'],
                            'total_cost' => $detail['amount'] * $detail['db_price'],
                        ]);
                    }
                    Notification::create([
                        'user_id' => $order->shop->user->id ?? null,
                        'shop_id' => $order->shop_id,
                        'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/36a74a55af0611584817_sjt6tv.jpg',
                        'title' => 'Bạn có đơn hàng mới',
                        'message' => 'Đơn hàng ' . $order->order_code . ' đã được tạo mới. Tổng tiền: ' . number_format($total_tong) . ' VND.',
                    ]);
                    $email = optional($order->shop->user)->email;

                    if (!empty($email)) {
                        Mail::to($email)->send(new OrderMail($order, $OrderDetail));
                    }


                    Log::info("✅ Đơn hàng được tạo mới!");
                }

                Log::info("✅ Xử lý hoàn tất!");
            }
        }
    }
    /**
     * Gọi API bằng cURL
     */
    private function sendApiRequest($url, $payload, $clientId, $token)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "client-id: $clientId",
            "token: $token"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
