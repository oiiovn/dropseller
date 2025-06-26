<?php

namespace App\Traits;

use App\Models\BalanceHistory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait BalanceLoggable
{


    public function generateAllBalanceHistories()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            DB::transaction(function () use ($user) {
                $userCode = $user->referral_code;
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

                    // Check đã có chưa
                    $exists = BalanceHistory::where('reference_id', $tran->id)
                        ->where('reference_type', 'transaction')
                        ->exists();

                    if (!$exists) {
                        BalanceHistory::create([
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
                }

                $user->total_amount = $runningBalance;
                $user->save();
            }, 3); // retry 3 lần nếu deadlock

            $count++;
        }

        return back()->with('success', "✅ Đã cập nhật lại số dư cho $count người dùng!");
    }
}
