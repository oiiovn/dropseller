<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReturnOrder;
use App\Models\Transaction;
use App\Models\Shop;
use Carbon\Carbon;

class ThanhToanDonHoan extends Command
{
    protected $signature = 'donhoan:thanhtoan';

    protected $description = 'Tạo thanh toán cho các đơn hoàn chưa thanh toán';

    public function handle(): void
    {
        $donHoanChuaThanhToan = ReturnOrder::where('payment_status', 'Chưa thanh toán')->get();
        $dem = 0;

        foreach ($donHoanChuaThanhToan as $don) {
            $shop = Shop::where('shop_id', $don->shop_id)->first();
            
            if (!$shop || !$shop->user || !$shop->user->referral_code) {
                $this->warn("❌ Không tìm thấy shop hoặc user cho shop_id: {$don->shop_id}");
                continue;
            }

            $transactionId = $this->generateUniqueTransactionId();

            Transaction::create([
                'bank' => 'DROP',
                'account_number' => $shop->user->referral_code,
                'transaction_date' => now(),
                'transaction_id' => $transactionId,
                'description' =>  $shop->user->referral_code . " Thanh toán đơn hoàn: {$don->order_code}" . " - " .  'Sản phẩm :'. $don->sku,
                'type' => 'IN',
                'amount' => $don->tong_tien,
            ]);

            // Cập nhật trạng thái đơn hoàn
            $don->update([
                'payment_status' => 'Đã thanh toán',
                'transaction_id' => $transactionId,
            ]);

            $this->info("✅ Đã thanh toán: {$don->order_code}");
            $dem++;
        }

        $this->info("🔁 Tổng cộng đã thanh toán {$dem} đơn hoàn.");
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'DH' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
