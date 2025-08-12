<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\User;
use App\Traits\BalanceLoggable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class TransactionObserver
{
    use BalanceLoggable;

    public function created(Transaction $tran)
    {
        $tran = $tran->fresh();

        // Chuẩn hoá
        $acc  = $tran->account_number ? strtoupper(trim((string)$tran->account_number)) : null;
        $desc = $tran->description ? trim($tran->description) : '';

        // 1) Ưu tiên theo account_number
        if ($acc) {
            $user = User::whereRaw('UPPER(referral_code) = ?', [$acc])->first();
            if ($user) {
                $this->generateBalanceHistoryForTransaction($user, $tran);
                return;
            }
        }

        // 2) Tách các token chữ/số trong description (bắt cả chữ + số như O02JP)
        //    VD: "MBVCB.10486082653.605764.O02JP.CT tu" -> ["MBVCB","10486082653","605764","O02JP","CT","tu"]
        $tokens = preg_split('/[^A-Za-z0-9]+/', strtoupper($desc), -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_values(array_unique($tokens));

        if (!empty($tokens)) {
            // Nếu referral_code là dạng chữ hoa/thường lẫn lộn, so sánh UPPER
            $users = User::whereIn(DB::raw('UPPER(referral_code)'), $tokens)->get();

            if ($users->count() === 1) {
                $this->generateBalanceHistoryForTransaction($users->first(), $tran);
                return;
            }

            if ($users->count() > 1) {
                // Tránh gán sai khi description có nhiều mã
                Log::warning('Ambiguous referral codes in transaction description', [
                    'transaction_id' => $tran->id,
                    'tokens'         => $tokens,
                    'matched_codes'  => $users->pluck('referral_code'),
                ]);
                return;
            }
        }

        Log::info('No referral matched for transaction', [
            'transaction_id' => $tran->id,
            'account_number' => $acc,
            'description'    => $desc,
        ]);
    }
}
