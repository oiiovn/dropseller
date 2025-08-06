<?php

namespace App\Traits;

use App\Models\BalanceHistory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait BalanceLoggable
{
    // ✅ Hàm này chỉ dùng để gọi thủ công, chạy lại toàn hệ thống
    public function generateAllBalanceHistories()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $this->generateBalanceHistoryForUser($user);
            $count++;
        }

        return back()->with('success', "✅ Đã cập nhật lại số dư cho $count người dùng!");
    }

    public function generateBalanceHistoryForTransaction(User $user, Transaction $tran)
    {
        DB::transaction(function () use ($user, $tran) {
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

            // Kiểm tra đã tạo BalanceHistory chưa
            $exists = BalanceHistory::where('reference_id', $tran->id)
                ->where('reference_type', 'transaction')
                ->exists();

            if (!$exists) {
                // Lấy balance gần nhất của user
                $latestBalance = BalanceHistory::where('user_id', $user->id)
                    ->latest('created_at')
                    ->value('balance_after') ?? 0;

                $newBalance = $latestBalance + $change;

                // Tạo bản ghi mới
                BalanceHistory::create([
                    'user_id' => $user->id,
                    'amount_change' => $change,
                    'balance_after' => $newBalance,
                    'type' => $balanceType,
                    'reference_id' => $tran->id,
                    'reference_type' => 'transaction',
                    'transaction_code' => $tran->transaction_id,
                    'note' => $tran->description,
                    'created_at' => $tran->transaction_date,
                    'updated_at' => $tran->transaction_date,
                ]);

                // Cập nhật tổng tiền
                $user->total_amount = $newBalance;
                $user->save();
            }
        });
    }
}
