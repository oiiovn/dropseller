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

            // 🔐 Chỉ lấy các giao dịch chứa đúng mã user và không chứa mã của người khác
            $transactions = $allTransactions->filter(function ($tran) use ($escapedCode, $user, $users) {
                $matched = preg_match('/(^|[\s:#|\-(),.])' . $escapedCode . '([\s:#|\-(),.]|$)/', $tran->description);

                if (!$matched) return false;

                foreach ($users as $otherUser) {
                    if ($otherUser->id === $user->id) continue;
                    $otherCode = preg_quote($otherUser->referral_code, '/');

                    if (
                        strpos($tran->description, $otherUser->referral_code) !== false &&
                        preg_match('/(^|[\s:#|\-(),.])' . $otherCode . '([\s:#|\-(),.]|$)/', $tran->description)
                    ) {
                        return false; // Có mã người khác → loại bỏ
                    }
                }

                return true;
            })->unique('id');

            foreach ($transactions as $tran) {
                $type = strtoupper(trim($tran->type));
                $change = $type === 'IN' ? (float)$tran->amount : -(float)$tran->amount;
                $runningBalance += $change;

                $balanceType = match ($tran->bank) {
                    'DROP' => $type === 'IN' ? 'refund' : 'order',
                    'ADS' => 'ads',
                    'PSP' => 'product_fee',
                    'QTD' => 'Monthly',
                    default => $type === 'IN' ? 'deposit' : 'withdraw',
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
