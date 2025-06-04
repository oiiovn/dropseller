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
            $userCode = $user->referral_code;

            // Xóa lịch sử cũ nếu có
            BalanceHistory::where('user_id', $user->id)->delete();
            $transactions = Transaction::whereRaw("description REGEXP '[[:<:]]{$userCode}[[:>:]]'")
                ->orderBy('transaction_date', 'asc')
                ->get();

            $runningBalance = 0;

            foreach ($transactions as $tran) {
                $change = $tran->type === 'IN' ? $tran->amount : -$tran->amount;

                switch ($tran->bank) {
                    case 'DROP':
                        $balanceType = $tran->type === 'IN' ? 'refund' : 'order';
                        break;
                    case 'ADS':
                        $balanceType = 'ads';
                        break;
                    case 'PSP':
                        $balanceType = 'product_fee';
                        break;
                    case 'QTD':
                        $balanceType = 'Monthly';
                        break;
                    default:
                        $balanceType = $tran->type === 'IN' ? 'deposit' : 'withdraw';
                        break;
                }

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

            // Cập nhật tổng số dư mới
            $user->total_amount = $runningBalance;
            $user->save();

            $count++;
        }

        return back()->with('success', "✅ Đã cập nhật lại số dư cho $count người dùng!");
    }
}
