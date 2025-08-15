<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoPaymentOrders extends Command
{
    protected $signature = 'orders:auto-payment';
    protected $description = 'Tự động thanh toán các đơn hàng chưa thanh toán';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('total_amount', '>', 0)->get(); // Chỉ lấy user có số dư

        foreach ($users as $user) {
            $this->thanhtoan($user);
        }
        $this->info("✅ Đã thanh toán tự động cho tất cả users có số dư!");
    }

    private function thanhtoan($user)
    {
        $shops = Shop::where('user_id', $user->id)->get();
        $allOrders = [];

        foreach ($shops as $shop) {
            $orders = Order::where('shop_id', $shop->shop_id)
                ->where('payment_status', 'Chưa thanh toán')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            $allOrders = [...$allOrders, ...$orders->all()];
        }

        foreach ($allOrders as $orderData) {
            DB::beginTransaction();
            try {
                // 🔒 1) Khóa order để tránh 2 tiến trình cùng thanh toán 1 đơn
                $order = Order::where('id', $orderData['id'])->lockForUpdate()->first();

                if (!$order || $order->payment_status !== 'Chưa thanh toán') {
                    DB::rollBack();
                    continue;
                }

                // 🔒 2) Khóa user trước khi kiểm tra số dư
                $userLocked = User::where('id', $user->id)->lockForUpdate()->first();

                // 🔒 3) Lấy số dư thực từ BalanceHistory cuối (đã được trait cập nhật chuẩn)
                $currentBalance = \App\Models\BalanceHistory::where('user_id', $userLocked->id)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->value('balance_after') ?? 0.0;

                // 4) Nếu đủ tiền thì tạo giao dịch OUT (Observer sẽ tự trừ & dồn)
                if ($currentBalance >= (float)$order->total_bill) {
                    $transactionId = $this->generateUniqueTransactionId();

                    // Cập nhật đơn (vẫn đang dưới lock)
                    $order->payment_status = 'Đã thanh toán';
                    $order->transaction_id = $transactionId;
                    $order->save();

                    // Tạo Transaction OUT -> TransactionObserver.created sẽ gọi BalanceLoggable
                    Transaction::create([
                        'bank'             => 'DROP',
                        'account_number'   => $userLocked->referral_code,
                        'transaction_date' => now(),
                        'transaction_id'   => $transactionId,
                        'description'      => $userLocked->referral_code . ' ' . $order->order_code,
                        'type'             => 'OUT',
                        'amount'           => $order->total_bill,
                    ]);

                    Notification::create([
                        'user_id' => $order->shop->user->id,
                        'shop_id' => $order->shop_id,
                        'image'   => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331596/c8dfdc013a52840cdd43_em29fp.jpg',
                        'title'   => 'Đơn hàng của bạn đã được thanh toán',
                        'message' => 'Đơn hàng ' . $order->order_code . ' đã được thanh toán số tiền ' . number_format($order->total_bill) . ' VND.',
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('❌ Lỗi thanh toán đơn hàng ID ' . $orderData['id'] . ': ' . $e->getMessage());
            }
        }
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'PT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    private function generateUniqueId($length = 8)
    {
        do {
            $id = random_int(pow(10, $length - 1), pow(10, $length) - 1);
        } while (Transaction::where('id', $id)->exists());
        return $id;
    }
}
