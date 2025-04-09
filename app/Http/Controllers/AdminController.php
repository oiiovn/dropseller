<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BalanceHistory;
use App\Models\User;

class AdminController extends Controller
{
    // public function generateBalanceHistory($userId)
    // {
    //     $user = User::findOrFail($userId);
    //     $userCode = $user->referral_code;

    //     // Xóa lịch sử cũ nếu có (đảm bảo không bị trùng)
    //     BalanceHistory::where('user_id', $user->id)->delete();

    //     // Lọc giao dịch liên quan tới user
    //     $transactions = Transaction::where('description', 'LIKE', "%$userCode%")
    //         ->orderBy('transaction_date', 'asc')
    //         ->get();

    //     $runningBalance = 0;

    //     foreach ($transactions as $tran) {
    //         $change = $tran->type === 'IN' ? $tran->amount : -$tran->amount;
    //         $runningBalance += $change;

    //         // Phân loại kiểu giao dịch (type) cho balance_histories
    //         switch ($tran->bank) {
    //             case 'DROP':
    //                 $balanceType = $tran->type === 'IN' ? 'refund' : 'order';
    //                 break;
    //             case 'ADS':
    //                 $balanceType = 'ads';
    //                 break;
    //             case 'PSP':
    //                 $balanceType = 'product_fee';
    //                 break;
    //             default:
    //                 $balanceType = $tran->type === 'IN' ? 'deposit' : 'withdraw';
    //                 break;
    //         }

    //         BalanceHistory::insert([
    //             'user_id' => $user->id,
    //             'amount_change' => $change,
    //             'balance_after' => $runningBalance,
    //             'type' => $balanceType,
    //             'reference_id' => $tran->id,
    //             'reference_type' => 'transaction',
    //             'transaction_code' => $tran->transaction_id ?? null, // 👈 dùng nếu có
    //             'note' => $tran->description,
    //             'created_at' => $tran->transaction_date,
    //             'updated_at' => $tran->transaction_date,
    //         ]);
    //     }

    //     // Cập nhật lại tổng số dư hiện tại
    //     $user->balance = $runningBalance;
    //     $user->save();

    //     return back()->with('success', '✅ Đã tạo lại lịch sử số dư cho user #' . $user->id);
    // }
}
