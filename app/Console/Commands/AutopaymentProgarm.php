<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\ProgramShop;

class AutopaymentProgarm extends Command
{
    protected $signature = 'PRG:auto-payment-progarm';
    protected $description = 'Tự động thanh toán các gói đăng sản phẩm';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('total_amount', '>', 0)->get();
        if ($users->isEmpty()) {
            $this->info("❌ Không có User nào đủ số dư để thanh toán gói sản phẩm.");
            Log::info("❌ Không có User nào đủ số dư để thanh toán gói sản phẩm.");
            return;
        }
        foreach ($users as $user) {
            $this->thanhtoan_prg($user);
        }
        $this->info("✅ Đã thanh toán tự động cho tất cả Users có số dư!");
        Log::info("✅ Đã thanh toán tự động cho tất cả Users có số dư!");
    }
    private function thanhtoan_prg($user)
    {
        Log::info("🔄 Đang xử lý thanh toán gói sản phẩm cho User: " . $user->name);
        $total_amount = $user->total_amount;
        $shops = Shop::where('user_id', $user->id)->get();
        $allPrg = [];
        foreach ($shops as $shop) {
            $prg = ProgramShop::where('shop_id', $shop->shop_id)
                ->where('status_payment', 'Chưa thanh toán')
                ->where('status_program', 'Đã hoàn thành')
                ->orderBy('created_at', 'asc')
                ->get();

            $allPrg = array_merge($allPrg, $prg->toArray());
        }
        foreach ($allPrg as $adData) {
            $transactionId = $this->generateUniqueTransactionId();
            $ad = ProgramShop::find($adData['id']);
            if (!$ad) {
                continue;
            }
            if ($total_amount >= $ad->total_amount) {
                if (is_numeric($ad->total) && $ad->total > 0) {
                    $user->total_amount = max(0, $user->total_amount - $ad->total_amount);
                    $user->save();
                } else {
                    Log::warning("❌ Giá trị `total` không hợp lệ cho chương trình");
                }

                $ad->update([
                    'status_payment' => 'Đã thanh toán',
                    'payment_code' => $transactionId,
                ]);
                Transaction::create([
                    'bank' => 'PSP',
                    'account_number' => $user->referral_code,
                    'transaction_date' => now(),
                    'transaction_id' => $transactionId,
                    'description' => $user->referral_code . " Thanh toán gói sản phẩm : " . $ad->program->name_program ?? 'Unknown',
                    'type' => 'OUT',
                    'amount' => $ad->total_amount,
                ]);
                Notification::create([
                    'user_id' => $user->id,
                    'shop_id' => $ad->shop_id,
                    'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331596/c8dfdc013a52840cdd43_em29fp.jpg',
                    'title' => 'Gói sản phẩm đã được thanh toán',
                    'message' => 'Hoá đơn  ' . $ad->program->name_program . 'gói sản phẩm đã được thanh toán số tiền ' . number_format($ad->total_amount) . ' VND.',
                ]);

                Log::info("✅ Thanh toán thành công cho quảng cáo: " . $ad->program->name_program);
            } else {
                Log::warning("❌ User {$user->name} không đủ số dư để thanh toán quảng cáo: " . $ad->program->name_program);
            }
        }
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'SP' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists());
        return $transactionId;
    }
}
