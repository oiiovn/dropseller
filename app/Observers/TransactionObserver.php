<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\User;
use App\Traits\BalanceLoggable;

class TransactionObserver
{
    use BalanceLoggable;

    public function created(Transaction $tran)
    {
        $tran = $tran->fresh();
        $user = null;

        // Ưu tiên: lấy từ account_number nếu có user
        if ($tran->account_number) {
            $user = User::where('referral_code', $tran->account_number)->first();
        }

        // Nếu không, dò các đoạn số được phân cách rõ ràng trong description
        if (!$user && preg_match_all('/(?<=[\s:#|\\-(),.])(\d{4,})(?=[\s:#|\\-(),.])/', $tran->description, $matches)) {
            foreach ($matches[1] as $candidate) {
                $user = User::where('referral_code', $candidate)->first();
                if ($user) break;
            }
        }

        // Nếu vẫn không tìm được user thì bỏ qua
        if (!$user) {
            return;
        }

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
            $tran->transaction_id
        );
    }
}
