<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BalanceHistory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\BalanceIssue;

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
    public function check_AI()
    {
        $users = User::all();
        $results = [];

        foreach ($users as $user) {
            $userLogs = [];
            $userLogs[] = "🧑 Kiểm tra user ID: {$user->id} ({$user->name})";

            $histories = BalanceHistory::where('user_id', $user->id)
                ->orderBy('id', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($histories->count() < 1) {
                $userLogs[] = "⚠️ Không có lịch sử giao dịch.";
                $results[] = $userLogs;
                continue;
            }

            // ✅ Kiểm tra giao dịch đầu tiên
            $first = $histories->first();
            if (abs($first->balance_after - $first->amount_change) > 0.01) {
                $prompt = "Giao dịch đầu tiên: amount_change = {$first->amount_change}, balance_after = {$first->balance_after}
            → Lẽ ra số dư sau phải bằng số tiền thay đổi. Có sai không?";

                $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Bạn là AI chuyên kiểm tra logic số dư sau giao dịch.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 150,
                ]);

                $content = $response['choices'][0]['message']['content'] ?? 'Không có phản hồi.';
                $userLogs[] = "❌ Lệch ở giao dịch đầu tiên ID {$first->id} → GPT: $content";

                BalanceIssue::create([
                    'user_id' => $user->id,
                    'balance_history_id' => $first->id,
                    'expected_balance' => $first->amount_change,
                    'actual_balance' => $first->balance_after,
                    'message' => $content,
                ]);
            } else {
                $userLogs[] = "✅ Giao dịch đầu tiên ID {$first->id} hợp lệ.";
            }

            // 🔁 Kiểm tra các giao dịch còn lại
            for ($i = 1; $i < $histories->count(); $i++) {
                $prev = $histories[$i - 1];
                $curr = $histories[$i];

                $expected = $prev->balance_after + $curr->amount_change;
                $actual = $curr->balance_after;

                if (abs($expected - $actual) > 0.01) {
                    $prompt = "Giao dịch trước: balance_after = {$prev->balance_after}
                Giao dịch hiện tại: amount_change = {$curr->amount_change}, balance_after = {$actual}
                Tính đúng: {$prev->balance_after} + ({$curr->amount_change}) = {$expected}
                Hệ thống ghi: {$actual}
                → Số dư có đúng không? Đúng/Sai + giải thích ngắn.";

                    $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Bạn là AI chuyên kiểm tra logic số dư sau giao dịch.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 150,
                    ]);

                    $content = $response['choices'][0]['message']['content'] ?? 'Không có phản hồi.';
                    $userLogs[] = "❌ Lệch ở ID {$curr->id} → GPT: $content";

                    BalanceIssue::create([
                        'user_id' => $user->id,
                        'balance_history_id' => $curr->id,
                        'expected_balance' => $expected,
                        'actual_balance' => $actual,
                        'message' => $content,
                    ]);
                } else {
                    $userLogs[] = "✅ ID {$curr->id} hợp lệ.";
                }
            }

            $userLogs[] = "───────────────────────────────";
        }

        return redirect()->back();
    }
}
