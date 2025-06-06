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
    $tran = $tran->fresh();
    $user = User::where('referral_code', $tran->account_number)->first();
    if (!$user && preg_match('/\d{4,}/', $tran->description, $matches)) {
        $user = User::where('referral_code', $matches[0])->first();
    }

    if ($user) {
        $this->generateBalanceHistoryForUser($user); // chỉ rebuild cho 1 user
    }
}

    
}
