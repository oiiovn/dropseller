<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Notification;
class UpdateReconciledOrders extends Command
{
    protected $signature = 'orders:update-reconciled';
    protected $description = 'Tự động cập nhật đơn hàng đã đối soát';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $dateThreshold = Carbon::now()->subDays(19);
        $transactions = Transaction::with('order')
            ->where('transaction_date', '<', $dateThreshold)
            ->whereHas('order', function ($query) {
                $query->where('reconciled', 1);
            })
            ->get();
    
        $updatedCount = 0;
    
        foreach ($transactions as $transaction) {
            if ($transaction->order) {
                $order = $transaction->order;
                $shopUser = $order->shop->user;
                // Nếu số tiền KHÁC với tổng đơn hàng
                if ($transaction->amount != $order->total_bill) {
                    $amountDiff = $transaction->amount - $order->total_bill;
                    // Cập nhật trạng thái đối soát lại
                    $order->update(['reconciled' => 0]);
                    $updatedCount++;
                    if ($amountDiff > 0) {
                        // Đã hoàn tiền > giá trị đơn, tạo giao dịch IN
                        Notification::create([
                            'user_id' => $shopUser->id,
                            'shop_id' => $order->shop_id,
                            'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/5d6b33d2d4816adf3390_iwkcee.jpg',
                            'title' => 'Đối soát đơn hàng',
                            'message' => 'Đơn hàng ' . $order->order_code . ' đã hoàn tiền đơn huỷ và thanh toán dư: ' . number_format($amountDiff) . ' VND.',
                        ]);
                        Transaction::create([
                            'bank' => 'DROP',
                            'account_number' => $shopUser->referral_code,
                            'transaction_date' => now(),
                            'transaction_id' => $this->generateUniqueTransactionId(),
                            'description' => $shopUser->referral_code . ' Thanh toán tiền huỷ đơn ' . $order->order_code . ' , Chúng tôi sẽ đối soát lại đơn hoàn cho bạn sau',
                            'type' => 'IN',
                            'amount' => $amountDiff,
                        ]);
                    } else {
                        $missingAmount = abs($amountDiff);
                        Notification::create([
                            'user_id' => $shopUser->id,
                            'shop_id' => $order->shop_id,
                            'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/5d6b33d2d4816adf3390_iwkcee.jpg',
                            'title' => 'Đối soát đơn hàng',
                            'message' => 'Đơn hàng ' . $order->order_code . ' đã thanh toán thiếu: ' . number_format($missingAmount) . ' VND. Chúng tôi đã cộng bù phần còn thiếu.',
                        ]);
    
                        Transaction::create([
                            'bank' => 'DROP',
                            'account_number' => $shopUser->referral_code,
                            'transaction_date' => now(),
                            'transaction_id' => $this->generateUniqueTransactionId(),
                            'description' => $shopUser->referral_code . ' Cộng bù tiền thiếu cho đơn hàng ' . $order->order_code,
                            'type' => 'OUT',
                            'amount' => $missingAmount,
                        ]);
                    }
                } else {
                    // Trường hợp khớp tiền
                    $order->update(['reconciled' => 0]);
    
                    Notification::create([
                        'user_id' => $shopUser->id,
                        'shop_id' => $order->shop_id,
                        'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/5d6b33d2d4816adf3390_iwkcee.jpg',
                        'title' => 'Đối soát đơn hàng',
                        'message' => 'Đơn hàng ' . $order->order_code . ' đã đối soát thành công.',
                    ]);
                }
            }
        }
    
        $this->info("✅ Đã cập nhật $updatedCount đơn hàng!");
    }
    

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'DS' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
