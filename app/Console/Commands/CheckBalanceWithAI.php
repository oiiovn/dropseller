<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\BalanceHistory;
use App\Models\BalanceIssue;

class CheckBalanceWithAI extends Command
{
    protected $signature = 'check:balance-ai';
    protected $description = 'Kiểm tra số dư sau, gọi GPT với mọi sai lệch và lưu log vào DB.';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $this->info("🧑 Kiểm tra user ID: {$user->id} ({$user->name})");

            $histories = BalanceHistory::where('user_id', $user->id)
                ->orderBy('id', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($histories->count() < 1) {
                $this->warn("⚠️ Không có lịch sử giao dịch.");
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
                $this->error("❌ Lệch ở giao dịch đầu tiên ID {$first->id} → GPT: $content");

                BalanceIssue::create([
                    'user_id' => $user->id,
                    'balance_history_id' => $first->id,
                    'expected_balance' => $first->amount_change,
                    'actual_balance' => $first->balance_after,
                    'message' => $content,
                ]);
            } else {
                $this->line("✅ Giao dịch đầu tiên ID {$first->id} hợp lệ.");
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
                    $this->error("❌ Lệch ở ID {$curr->id} → GPT: $content");

                    BalanceIssue::create([
                        'user_id' => $user->id,
                        'balance_history_id' => $curr->id,
                        'expected_balance' => $expected,
                        'actual_balance' => $actual,
                        'message' => $content,
                    ]);
                } else {
                    $this->line("✅ ID {$curr->id} hợp lệ.");
                }
            }

            $this->line("───────────────────────────────");
        }
    }
}
