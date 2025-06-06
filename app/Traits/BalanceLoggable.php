<?php

namespace App\Traits;

use App\Models\BalanceHistory;
use App\Models\Transaction;
use App\Models\User;

trait BalanceLoggable
{
    public function generateAllBalanceHistories()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $userCode = preg_quote($user->referral_code, '/');

            // Xoá lịch sử cũ
            BalanceHistory::where('user_id', $user->id)->delete();

            // Tìm transaction có chứa đúng referral_code với phân cách rõ ràng
            $transactions = Transaction::whereRaw("description REGEXP '(^|[[:space:]:#|\\-(),\\.]){$userCode}([[:space:]:#|\\-(),\\.]|$)'")
                ->orderBy('transaction_date', 'asc')
                ->get();

            $runningBalance = 0;

            foreach ($transactions as $tran) {
                $change = $tran->type === 'IN' ? $tran->amount : -$tran->amount;

                $balanceType = match ($tran->bank) {
                    'DROP' => $tran->type === 'IN' ? 'refund' : 'order',
                    'ADS' => 'ads',
                    'PSP' => 'product_fee',
                    'QTD' => 'Monthly',
                    default => $tran->type === 'IN' ? 'deposit' : 'withdraw',
                };

                $runningBalance += $change;

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
            }

            // Cập nhật số dư mới
            $user->total_amount = $runningBalance;
            $user->save();

            $count++;
        }

        return back()->with('success', "✅ Đã cập nhật lại số dư cho $count người dùng!");
    }
}
