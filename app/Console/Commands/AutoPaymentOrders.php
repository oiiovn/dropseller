<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Notification;
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
        $total_amount = $user->total_amount;
        $shops = Shop::where('user_id', $user->id)->get();
        $allOrders = [];

        foreach ($shops as $shop) {
            $orders = Order::where('shop_id', $shop->shop_id)
                ->where('payment_status', 'Chưa thanh toán')
                ->orderBy('created_at', 'asc')
                ->get();

            $allOrders = array_merge($allOrders, $orders->toArray());
        }

        foreach ($allOrders as $orderData) {
            $transactionId = $this->generateUniqueTransactionId();
            $uniqueId = $this->generateUniqueId();
            $order = Order::find($orderData['id']);

            if (!$order) {
                continue;
            }
            if ($total_amount >= $order->total_bill) {
                $order->payment_status = 'Đã thanh toán';
                $order->transaction_id = $transactionId;
                $order->save();
                $total_amount -= $order->total_bill;
                $user->total_amount = $total_amount;
                $user->save();
                Transaction::create([
                    'id' => $uniqueId,
                    'bank' => 'DROP',
                    'account_number' => $user->referral_code,
                    'transaction_date' => now(),
                    'transaction_id' => $transactionId,
                    'description' => $user->referral_code . ' ' . $order->order_code,
                    'type' => 'OUT',
                    'amount' => $order->total_bill,
                ]);
                Notification::create([
                    'user_id' => $order->shop->user->id, 
                    'shop_id' => $order->shop_id,
                    'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331596/c8dfdc013a52840cdd43_em29fp.jpg',
                    'title' => 'Đơn hàng của bạn đã được thanh toán',
                    'message' => 'Đơn hàng ' . $order->order_code . ' đã được thanh toán số tiền ' . number_format($order->total_bill) . ' VND.',
                ]);
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
