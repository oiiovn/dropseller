<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Models\BalanceHistory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\BalanceIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
    public function check_code()
    {
        // Kiểm tra quyền truy cập
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này');
        }

        // Lấy tất cả các mã từ cơ sở dữ liệu để kiểm tra
        $referralCodes = User::pluck('referral_code')->toArray();
        $duplicateCodes = array_filter(array_count_values($referralCodes), function ($count) {
            return $count > 1;
        });

        $results = [
            'duplicate_codes' => $duplicateCodes,
            'invalid_format' => [],
            'stats' => [
                'total' => count($referralCodes),
                'unique' => count(array_unique($referralCodes)),
                'duplicates' => count($duplicateCodes),
            ]
        ];

        // Kiểm tra định dạng mã
        $users = User::all();
        foreach ($users as $user) {
            $code = $user->referral_code;

            // Kiểm tra định dạng mã (5 ký tự chữ và số viết hoa)
            if ($code && (!preg_match('/^[A-Z0-9]{5}$/', $code))) {
                $results['invalid_format'][] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'code' => $code,
                    'issue' => 'Mã không đúng định dạng (cần 5 ký tự chữ hoa và số)'
                ];
            }
        }

        // Kiểm tra xung đột với các mã giao dịch
        $transactionCodes = Transaction::pluck('transaction_id')->toArray();
        $conflictCodes = array_intersect($referralCodes, $transactionCodes);

        if (!empty($conflictCodes)) {
            $results['transaction_conflicts'] = $conflictCodes;
        }

        // Ghi lại log kết quả kiểm tra
        Log::info('Admin check_code: ' . json_encode($results));

        // Hiển thị kết quả trên giao diện người dùng
        session(['code_check_results' => $results]);

        return view('admin.code_check', [
            'results' => $results,
            'users' => $users
        ]);
    }

    /**
     * Tự động sửa mã không hợp lệ
     */
    public function fix_invalid_codes()
    {
        // Kiểm tra quyền truy cập
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này');
        }

        $users = User::whereRaw("LENGTH(referral_code) != 5 OR referral_code NOT REGEXP '^[A-Z0-9]{5}$'")
            ->orWhereNull('referral_code')
            ->get();

        $fixed = 0;

        foreach ($users as $user) {
            $newCode = $this->generateUniqueReferralCode();
            $user->referral_code = $newCode;
            $user->save();
            $fixed++;

            Log::info("Fixed user ID {$user->id} referral code to: {$newCode}");
        }

        // Sửa mã trùng lặp
        $referralCodes = User::pluck('referral_code', 'id')->toArray();
        $codeCount = array_count_values($referralCodes);

        foreach ($codeCount as $code => $count) {
            if ($count > 1) {
                // Tìm tất cả người dùng có mã trùng lặp
                $duplicateUsers = User::where('referral_code', $code)->get();

                // Giữ nguyên mã cho người dùng đầu tiên, thay đổi cho những người còn lại
                for ($i = 1; $i < count($duplicateUsers); $i++) {
                    $user = $duplicateUsers[$i];
                    $newCode = $this->generateUniqueReferralCode();
                    $user->referral_code = $newCode;
                    $user->save();
                    $fixed++;

                    Log::info("Fixed duplicate code for user ID {$user->id}: {$code} -> {$newCode}");
                }
            }
        }

        return redirect()->route('admin.check_code')->with('success', "Đã sửa {$fixed} mã không hợp lệ");
    }

    /**
     * Tạo mã giới thiệu dựa trên cấu hình
     */
    private function generateUniqueReferralCode(): string
    {
        // Lấy mã tiền tố từ cấu hình (ví dụ: "DS")
        $prefix = config('app.referral_code_prefix', 'DS');

        // Lấy độ dài phần số (mặc định: 3 chữ số)
        $numberLength = config('app.referral_code_number_length', 3);

        // Bắt đầu từ số được cấu hình hoặc mặc định từ 1
        $startNumber = config('app.referral_code_start', 1);

        // Tìm mã cuối cùng đã được sử dụng để tạo mã tiếp theo
        $lastCode = User::where('referral_code', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(referral_code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('referral_code');

        if ($lastCode) {
            // Trích xuất phần số từ mã cuối cùng
            $lastNumber = (int) substr($lastCode, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $startNumber;
        }

        // Tạo mã mới với số được định dạng theo độ dài cấu hình
        $code = $prefix . str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);

        // Kiểm tra xem mã đã tồn tại chưa và tăng số nếu cần
        while (User::where('referral_code', $code)->exists()) {
            $nextNumber++;
            $code = $prefix . str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    /**
     * Xác thực mã truy cập admin
     */
    public function verifyAccessCode(Request $request)
    {
        // Lấy mã truy cập từ config (ở đây mã được đặt cố định trong config)
        $validAccessCode = config('admin.access_code', 'ADMIN_1994');

        $inputCode = $request->input('access_code');

        if ($inputCode === $validAccessCode) {
            // Lưu trạng thái xác thực vào session
            session(['admin_access_verified' => true, 'admin_verified_at' => now()]);

            // Log hoạt động
            Log::info("Admin access code verified by user: " . auth()->user()->id);

            return redirect()->back()->with([
                'access_code_status' => 'success',
                'access_code_message' => 'Mã truy cập hợp lệ. Quyền quản trị cao cấp đã được kích hoạt.'
            ]);
        }

        // Log thất bại
        Log::warning("Failed admin access code attempt by user: " . auth()->user()->id . ", code: " . $inputCode);

        return redirect()->back()->with([
            'access_code_status' => 'error',
            'access_code_message' => 'Mã truy cập không hợp lệ. Vui lòng thử lại.'
        ]);
    }

    /**
     * Kiểm tra xác thực mã truy cập admin (để sử dụng trong middleware)
     */
    public static function isAdminAccessVerified()
    {
        // Kiểm tra xem admin đã xác thực mã truy cập chưa
        if (!session()->has('admin_access_verified') || !session()->has('admin_verified_at')) {
            return false;
        }

        // Kiểm tra xem xác thực có quá cũ không (ví dụ: hết hạn sau 1 giờ)
        $verifiedAt = session('admin_verified_at');
        $expiresAt = Carbon::parse($verifiedAt)->addHour();

        if (now()->greaterThan($expiresAt)) {
            // Xác thực đã hết hạn
            session()->forget(['admin_access_verified', 'admin_verified_at']);
            return false;
        }

        return session('admin_access_verified') === true;
    }
}
