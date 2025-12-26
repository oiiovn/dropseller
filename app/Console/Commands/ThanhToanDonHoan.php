<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReturnOrder;
use App\Models\Transaction;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThanhToanDonHoan extends Command
{
    protected $signature = 'donhoan:thanhtoan';

    protected $description = 'Tạo thanh toán cho các đơn hoàn chưa thanh toán';

    public function handle(): void
    {
        // Thay đổi 1: Sử dụng xử lý theo batch thay vì lấy tất cả đơn một lúc
        $batchSize = 10;
        $dem = 0;
        $errors = 0;

        // Thay đổi 2: Sử dụng chunkById để xử lý từng nhóm nhỏ đơn hàng
        ReturnOrder::where('payment_status', 'Chưa thanh toán')
            ->chunkById($batchSize, function ($donHoanChuaThanhToan) use (&$dem, &$errors) {
                foreach ($donHoanChuaThanhToan as $don) {
                    try {
                        // Thay đổi 3: Thêm DB transaction để đảm bảo tính nhất quán
                        DB::beginTransaction();
                        
                        $shop = Shop::where('shop_id', $don->shop_id)->first();
                        
                        if (!$shop || !$shop->user || !$shop->user->referral_code) {
                            $this->warn("❌ Không tìm thấy shop hoặc user cho shop_id: {$don->shop_id}");
                            DB::rollBack();
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

                        // Thay đổi 4: Commit transaction khi thành công
                        DB::commit();
                        $this->info("✅ Đã thanh toán: {$don->order_code}");
                        $dem++;
                        
                        // Thay đổi 5: Thêm một khoảng thời gian nghỉ nhỏ giữa các xử lý
                        usleep(100000); // 100ms delay
                    } catch (\Exception $e) {
                        // Thay đổi 6: Xử lý lỗi chi tiết và rollback khi gặp vấn đề
                        DB::rollBack();
                        $errors++;
                        Log::error("Lỗi xử lý đơn hoàn {$don->order_code}: " . $e->getMessage());
                        $this->error("❌ Lỗi xử lý đơn: {$don->order_code} - " . $e->getMessage());
                    }
                }
            });

        // Thay đổi 7: Báo cáo chi tiết cả thành công và thất bại
        $this->info("🔁 Tổng cộng đã thanh toán {$dem} đơn hoàn. Thất bại: {$errors} đơn.");
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'DH' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
