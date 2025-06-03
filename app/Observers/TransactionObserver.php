<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\User;
use App\Traits\BalanceLoggable;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    use BalanceLoggable;

    public function created(Transaction $tran)
    {
        // Ép lấy lại bản ghi đầy đủ từ DB
        $tran = $tran->fresh();
        $user = User::where('referral_code', $tran->account_number)->first();
<<<<<<< HEAD
        if (!$user) return;
=======
        if (!$user && preg_match('/\d{4,}/', $tran->description, $matches)) {
            $user = User::where('referral_code', $matches[0])->first();
        }
>>>>>>> c10092977d5f599cce749af994c469c1a3a65ad6
    
        $change = $tran->type === 'IN' ? $tran->amount : -$tran->amount;
    
        $this->generateAllBalanceHistories(
            $user,
            $change,
            match ($tran->bank) {
                'DROP' => $tran->type === 'IN' ? 'refund' : 'order',
                'ADS' => 'ads',
                'PSP' => 'product_fee',
                'QTD' => 'Monthly',
                default => $tran->type === 'IN' ? 'deposit' : 'withdraw',
            },
            $tran->description,
            $tran->id,
            'transaction',
            $tran->transaction_id // ✅ sẽ có giá trị sau khi fresh
        );
    }
    
}
