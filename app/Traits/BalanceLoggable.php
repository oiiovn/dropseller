<?php

namespace App\Traits;

use App\Models\BalanceHistory;
use App\Models\Transaction;
use App\Models\User;

trait BalanceLoggable
{
   public function generateBalanceHistoryForUser($user)
{
    BalanceHistory::where('user_id', $user->id)->delete();

    $transactions = Transaction::whereRaw("description REGEXP '[[:<:]]{$user->referral_code}[[:>:]]'")
        ->orderBy('transaction_date', 'asc')
        ->get();

    $runningBalance = 0;

    foreach ($transactions as $tran) {
        $change = $tran->type === 'IN' ? $tran->amount : -$tran->amount;

        switch ($tran->bank) {
            case 'DROP': $balanceType = $tran->type === 'IN' ? 'refund' : 'order'; break;
            case 'ADS': $balanceType = 'ads'; break;
            case 'PSP': $balanceType = 'product_fee'; break;
            case 'QTD': $balanceType = 'Monthly'; break;
            default: $balanceType = $tran->type === 'IN' ? 'deposit' : 'withdraw'; break;
        }

        $runningBalance += $change;

        BalanceHistory::create([
            'user_id'         => $user->id,
            'amount_change'   => $change,
            'balance_after'   => $runningBalance,
            'type'            => $balanceType,
            'reference_id'    => $tran->id,
            'reference_type'  => 'transaction',
            'transaction_code'=> $tran->transaction_id,
            'note'            => $tran->description,
            'created_at'      => $tran->transaction_date,
            'updated_at'      => $tran->transaction_date,
        ]);
    }

    $user->total_amount = $runningBalance;
    $user->save();

    // return hoặc log gì đó nếu cần
}

}
