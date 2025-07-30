<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Traits\BalanceLoggable;

class RebuildBalanceHistory extends Command
{
    use BalanceLoggable;

    protected $signature = 'balance:rebuild {user_id}';
    protected $description = 'Chạy lại balance_histories cho 1 user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ Không tìm thấy user với ID {$userId}");
            return;
        }

        // ✅ XÓA HẾT balance_histories của user
        $deleted = \App\Models\BalanceHistory::where('user_id', $user->id)->delete();
        $this->info("🧹 Đã xoá {$deleted} dòng balance_histories cũ của user ID {$userId}");

        // ✅ Ghi lại từ đầu
        $this->generateBalanceHistoryForUser($user);

        $this->info("✅ Đã cập nhật balance_histories mới cho user ID {$userId}");
    }
}
