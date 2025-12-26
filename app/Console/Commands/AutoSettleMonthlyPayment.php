<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMonthlyReport;
use App\Models\Transaction;
use Carbon\Carbon;

class AutoSettleMonthlyPayment extends Command
{
    protected $signature = 'auto:settle-monthly {month?}';
    protected $description = 'Tự động tạo giao dịch thanh toán chênh lệch mỗi tháng. VD: auto:settle-monthly 2025-08';

    public function handle()
    {
        // Lấy tháng từ tham số hoặc mặc định là tháng trước
        $targetMonth = $this->argument('month') ?? Carbon::now()->subMonth()->format('Y-m');
        
        // Validate format tháng
        if (!preg_match('/^\d{4}-\d{2}$/', $targetMonth)) {
            $this->error('❌ Format tháng không hợp lệ. Sử dụng format: YYYY-MM (VD: 2025-08)');
            return;
        }
        
        $this->info("📅 Đang xử lý thanh toán quyết toán tháng: $targetMonth");

        $reports = UserMonthlyReport::where('month', $targetMonth)
        ->where('status_payment', 'Chưa thanh toán')
            ->where('tien_phai_thanh_toan', '!=', 0)
            ->get();
        foreach ($reports as $report) {
            Transaction::create([
                'bank' => 'QTD',
                'account_number' => $report->user->referral_code,
                'transaction_date' => now(),
                'transaction_id' =>  $report->id_QT,
                'description' => $report->user->referral_code . " Quyết toán đơn hàng tháng " . $targetMonth,
                'type' => $report->tien_phai_thanh_toan > 0 ? 'IN' : 'OUT',
                'amount' => abs($report->tien_phai_thanh_toan),
            ]);
            $report->update(['status_payment' => 'Đã thanh toán']);
        }
        $this->info("Đã tạo {$reports->count()} giao dịch thanh toán tháng $targetMonth.");
    }
}
