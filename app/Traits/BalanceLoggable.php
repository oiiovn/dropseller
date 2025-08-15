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
            $change = (float)($tran->type === 'IN' ? $tran->amount : -$tran->amount);

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
                    $balanceType = 'Monthly'; // giữ nguyên nếu enum DB đang là 'Monthly'
                    break;
                default:
                    $balanceType = $tran->type === 'IN' ? 'deposit' : 'withdraw';
                    break;
            }

            // Đã tạo rồi thì thôi
            $exists = BalanceHistory::where('reference_id', $tran->id)
                ->where('reference_type', 'transaction')
                ->exists();
            if ($exists) return;
            // 0) Khóa dải tương lai để chặn insert cùng/lớn hơn thời điểm giao dịch
            BalanceHistory::where('user_id', $user->id)
                ->where('created_at', '>=', $tran->transaction_date)
                ->orderBy('created_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']); // chỉ để tạo lock, không dùng dữ liệu

            // 1) Lấy số dư ngay trước (hoặc tại) thời điểm giao dịch
            $prev = BalanceHistory::where('user_id', $user->id)
                ->where('created_at', '<=', $tran->transaction_date)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $baseBalance = (float)($prev?->balance_after ?? 0);
            $newBalance  = $baseBalance + $change;

            // 2) Tạo bản ghi đúng thời điểm giao dịch
            $bh = BalanceHistory::create([
                'user_id'          => $user->id,
                'amount_change'    => $change,
                'balance_after'    => $newBalance,
                'type'             => $balanceType,
                'reference_id'     => $tran->id,
                'reference_type'   => 'transaction',
                'transaction_code' => $tran->transaction_id,
                'note'             => $tran->description,
                'created_at'       => $tran->transaction_date,
                'updated_at'       => $tran->transaction_date,
            ]);

            // 3) Dồn chỉnh các bản ghi SAU thời điểm giao dịch (có khoá)
            $futureQuery = BalanceHistory::where('user_id', $user->id)
                ->where(function ($q) use ($bh) {
                    $q->where('created_at', '>', $bh->created_at)
                        ->orWhere(function ($q2) use ($bh) {
                            $q2->where('created_at', $bh->created_at)
                                ->where('id', '>', $bh->id);
                        });
                })
                ->lockForUpdate();

            $futureIds = $futureQuery->pluck('id');

            if ($futureIds->isNotEmpty()) {
                // ✅ increment giữ đúng kiểu decimal, tránh sai số
                BalanceHistory::whereIn('id', $futureIds)
                    ->increment('balance_after', $change);
            }

            // 4) Cập nhật tổng tiền hiện tại
            $latest = BalanceHistory::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->value('balance_after') ?? 0;

            $user->total_amount = $latest;
            $user->save();
        });
    }
    // App/Traits/BalanceLoggable.php

    public function generateBalanceHistoryForUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Lấy tất cả transaction “thuộc” user này
            // Ưu tiên: account_number == referral_code
            // Dự phòng: description có chứa referral_code theo token (không match chuỗi con mơ hồ)
            $code = strtoupper(trim((string)$user->referral_code));
            $safe = preg_quote($code, '/'); // escape cho regex

            $transactions = Transaction::query()
                ->where(function ($q) use ($code, $safe) {
                    $q->whereRaw('UPPER(account_number) = ?', [$code])
                        ->orWhereRaw("UPPER(description) REGEXP ?", [
                            "(^|[^A-Z0-9]){$safe}([^A-Z0-9]|$)"
                        ]);
                })
                ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($transactions as $tran) {
                // Hàm này đã xử lý chèn quá khứ + dồn tương lai + khoá
                $this->generateBalanceHistoryForTransaction($user, $tran);
            }

            // Cập nhật lại tổng số dư = bản ghi cuối cùng
            $latest = BalanceHistory::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->value('balance_after') ?? 0;

            $user->total_amount = $latest;
            $user->save();
        });
    }
}
