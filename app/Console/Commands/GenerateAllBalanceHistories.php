<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class GenerateAllBalanceHistories extends Command
{
    protected $signature = 'balance:rebuild-all';
    protected $description = 'Xoá toàn bộ lịch sử cũ và tạo lại từ đầu.';

    public function handle()
    {
        $this->warn('⚠️ Đang xoá toàn bộ dữ liệu cũ trong bảng balance_histories...');

        // Xóa toàn bộ dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('balance_histories')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->info('🧹 Đã xoá xong. Bắt đầu tạo lịch sử số dư mới...');

        $users = User::all();
        $allTransactions = Transaction::orderBy('transaction_date', 'asc')->get();

        $userCount = 0;
        $newLogs = 0;

        foreach ($users as $user) {
            $userCode = $user->referral_code;
            $escapedCode = preg_quote($userCode, '/');
            $runningBalance = 0;

            // ❗ Dùng regex mới để lọc giao dịch match chính xác hơn
            $transactions = $allTransactions->filter(function ($tran) use ($escapedCode) {
                return preg_match('/(^|[\s:#|\-(),.])' . $escapedCode . '([\s:#|\-(),.]|$)/', $tran->description);
            });

            foreach ($transactions as $tran) {
                $change = $tran->type === 'IN' ? (float)$tran->amount : -(float)$tran->amount;
                $runningBalance += $change;

                $balanceType = match ($tran->bank) {
                    'DROP' => $tran->type === 'IN' ? 'refund' : 'order',
                    'ADS' => 'ads',
                    'PSP' => 'product_fee',
                    'QTD' => 'Monthly',
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

        $this->info("✅ Đã xử lý $userCount người dùng, tạo mới $newLogs bản ghi lịch sử.");
    }
}
