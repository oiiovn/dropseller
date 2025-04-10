<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\BalanceHistory;

class GenerateAllBalanceHistories extends Command
{
    protected $signature = 'balance:rebuild-all';
    protected $description = 'Chỉ thêm mới các lịch sử số dư chưa có, không xoá dữ liệu cũ.';
    public function handle()
    {
        $this->info('🚀 Đang tạo lịch sử số dư mới (không xoá dữ liệu cũ)...');
        $users = User::all();
        $userCount = 0;
        $newLogs = 0;
        foreach ($users as $user) {
            $userCode = $user->referral_code;

            // Lấy balance sau cùng nếu có
            $latest = BalanceHistory::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->first();

            $runningBalance = $latest?->balance_after ?? 0;

            // Lọc các giao dịch theo referral_code
            $transactions = Transaction::where('description', 'LIKE', "%$userCode%")
                ->orderBy('transaction_date', 'asc')
                ->get();

            foreach ($transactions as $tran) {
                $exists = BalanceHistory::where('reference_id', $tran->id)
                    ->where('reference_type', 'transaction')
                    ->exists();

                if ($exists) continue;

                $change = $tran->type === 'IN' ? (float)$tran->amount : -(float)$tran->amount;
                $runningBalance += $change;

                $balanceType = match ($tran->bank) {
                    'DROP' => $tran->type === 'IN' ? 'refund' : 'order',
                    'ADS' => 'ads',
                    'PSP' => 'product_fee',
                    default => $tran->type === 'IN' ? 'deposit' : 'withdraw',
                };

                BalanceHistory::insert([
                    'user_id' => $user->id,
                    'amount_change' => $change,
                    'balance_after' => $runningBalance,
                    'type' => $balanceType,
                    'reference_id' => $tran->id,
                    'reference_type' => 'transaction',
                    'transaction_code' => $tran->transaction_id,
                    'note' => $tran->description,
                    'created_at' => $tran->transaction_date,
                    'updated_at' => $tran->transaction_date,
                ]);

                $newLogs++;
            }

            $user->total_amount = round($runningBalance, 2);
            $user->save();
            $userCount++;
        }

        $this->info("✅ Đã xử lý $userCount người dùng, thêm mới $newLogs bản ghi lịch sử.");
    }
}
