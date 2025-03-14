<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Shop;
use App\Models\ADS;
use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoPaymentAds extends Command
{
    protected $signature = 'ads:auto-payment';
    protected $description = 'Tự động thanh toán các chiến dịch quảng cáo chưa thanh toán';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('total_amount', '>', 0)->get();
        if ($users->isEmpty()) {
            $this->info("❌ Không có User nào đủ số dư để thanh toán quảng cáo.");
            Log::info("❌ Không có User nào đủ số dư để thanh toán quảng cáo.");
            return;
        }
        foreach ($users as $user) {
            $this->thanhtoan_ads($user);
        }
        $this->info("✅ Đã thanh toán tự động cho tất cả Users có số dư!");
        Log::info("✅ Đã thanh toán tự động cho tất cả Users có số dư!");
    }
    private function thanhtoan_ads($user)
    {
        Log::info("🔄 Đang xử lý thanh toán quảng cáo cho User: " . $user->name);
        $total_amount = $user->total_amount;
        $shops = Shop::where('user_id', $user->id)->get();
        $allAds = [];
        foreach ($shops as $shop) {
            $ads = ADS::where('shop_id', $shop->shop_id)
                ->where('payment_status', 'Chưa thanh toán')
                ->orderBy('created_at', 'asc')
                ->get();
            $allAds = array_merge($allAds, $ads->toArray());
        }
        foreach ($allAds as $adData) {
            $transactionId = $this->generateUniqueTransactionId();
            $uniqueId = $this->generateUniqueId();
            $ad = ADS::find($adData['id']);
            if (!$ad) {
                continue;
            }
            if ($total_amount >= $ad->total_amount) {
                if (is_numeric($ad->total) && $ad->total > 0) {
                    $user->total_amount = max(0, $user->total_amount - $ad->total_amount); // Đảm bảo không âm
                    $user->save();
                } else {
                    Log::warning("❌ Giá trị `total` không hợp lệ cho quảng cáo: " . $ad->invoice_id);
                }
                
                $ad->update([
                    'payment_status' => 'Đã thanh toán',
                    'payment_code' => $transactionId,
                ]);
                Transaction::create([
                    'id' => $uniqueId,
                    'bank' => 'ADS',
                    'account_number' => $user->referral_code,
                    'transaction_date' => now(),
                    'transaction_id' => $transactionId,
                    'description' => $user->referral_code . " Thanh toán quảng cáo: " . $ad->invoice_id,
                    'type' => 'OUT',
                    'amount' => $ad->total_amount,
                ]);

                // Gửi thông báo
                Notification::create([
                    'user_id' => $user->id,
                    'shop_id' => $ad->shop_id,
                    'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331596/c8dfdc013a52840cdd43_em29fp.jpg',
                    'title' => 'Chiến dịch quảng cáo đã được thanh toán',
                    'message' => 'Chiến dịch quảng cáo ' . $ad->invoice_id . ' đã được thanh toán số tiền ' . number_format($ad->total_amount) . ' VND.',
                ]);

                Log::info("✅ Thanh toán thành công cho quảng cáo: " . $ad->invoice_id);
            } else {
                Log::warning("❌ User {$user->name} không đủ số dư để thanh toán quảng cáo: " . $ad->invoice_id);
            }
        }
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'QC' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
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
