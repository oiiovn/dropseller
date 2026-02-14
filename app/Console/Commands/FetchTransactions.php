<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\DebtDistribution;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FetchTransactions extends Command
{
    // Tên lệnh để chạy thủ công
    protected $signature = 'fetch:transactions';

    // Mô tả lệnh
    protected $description = 'Fetch transactions from API and store them in the database';

    public function handle()
    {
        $url = config('pay2s.transactions_url', 'https://my.pay2s.vn/userapi/transactions');
        $token = base64_encode(config('pay2s.secret_key'));
        $accountsStr = config('pay2s.bank_accounts', '46241987');
        $accounts = array_filter(array_map('trim', explode(',', $accountsStr)));
        $begin = config('pay2s.fetch_begin', '22/08/2025');
        $end = config('pay2s.fetch_end', '20/11/2029');

        $transactions = [];
        foreach ($accounts as $account) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'pay2s-token' => $token,
            ])->post($url, [
                'bankAccounts' => $account,
                'begin' => $begin,
                'end' => $end,
            ]);

            if ($response->failed()) {
                $this->warn("Pay2s API failed for account {$account}: " . $response->status());
                continue;
            }
            $list = $response->json()['transactions'] ?? [];
            foreach ($list as $t) {
                $transactions[] = $t;
            }
        }

        foreach ($transactions as $transaction) {
            if (Transaction::where('transaction_id', $transaction['transaction_id'])->exists()) {
                continue; 
            }
            Transaction::create([
                'bank' => $transaction['bank'],
                'account_number' => $transaction['account_number'],
                'transaction_date' => $transaction['transaction_date'],
                'transaction_id' => $transaction['transaction_id'],
                'amount' => $transaction['amount'],
                'type' => $transaction['type'],
                'description' => $transaction['description']
            ]);

            // Chi tiền (OUT): nếu nội dung chứa mã giao dịch phân bổ nợ → ghi nhận đã thanh toán
            if (isset($transaction['type']) && strtoupper($transaction['type']) === 'OUT' && !empty($transaction['description'])) {
                $description = (string) $transaction['description'];
                $pending = DebtDistribution::where('status', 'pending')->get();
                foreach ($pending as $dist) {
                    if (str_contains($description, $dist->transaction_code)) {
                        $paidAt = isset($transaction['transaction_date'])
                            ? Carbon::parse($transaction['transaction_date'])
                            : now();
                        $dist->update([
                            'status' => 'paid',
                            'paid_at' => $paidAt,
                            'bank_transaction_ref' => $transaction['transaction_id'] ?? null,
                        ]);
                        Log::info("Đã ghi nhận thanh toán nợ: distribution_id={$dist->id}, transaction_code={$dist->transaction_code}, bank_ref=" . ($transaction['transaction_id'] ?? ''));
                        break;
                    }
                }
            }
        
            $user = User::whereRaw("? LIKE CONCAT('%', referral_code, '%')", [$transaction['description']])->first();
            if ($user) {
                Notification::create([
                    'user_id' => $user->id, 
                    'shop_id' => null,
                    'image' => 'https://res.cloudinary.com/dup7bxiei/image/upload/v1739331620/e738bc7c592fe771be3e_yvabzb.jpg',
                    'title' => 'Bạn có giao dịch mới',
                    'message' => 'Bạn vừa nạp ' . number_format($transaction['amount']) . ' VND.',
                ]);
            } else {
                Log::error("Không tìm thấy user có referral_code: " . $transaction['description']);
            }
        }

        $this->info('Transactions fetched and stored successfully.');


        $users = User::all();
            
            foreach ($users as $user) {
                $balace = $user->balance;
                $totalAmount = 0;
                $userCode = $user->referral_code;
                
                $transactions = Transaction::where('description', 'LIKE', "%$userCode%")
                    ->get();
                
                $Transactions_Drop = Transaction::with('order')
                    ->where('description', 'LIKE', "%$userCode%")
                    ->where('bank', 'DROP')
                    ->whereHas('order', function ($query) {
                        $query->where('reconciled', 1);
                    })
                    ->get();
                foreach ($transactions as $transaction) {
                    if ($transaction->type == 'IN') {
                        $totalAmount += $transaction->amount;
                    } elseif ($transaction->type == 'OUT') {
                        $totalAmount -= $transaction->amount;
                    }
                }
                
            }
            $this->info(' cộng tiền cho user');

        // $users = User::all();
            
        //     foreach ($users as $user) {
        //         $balace = $user->balance;
        //         $totalAmount = 0;
        //         $userCode = $user->referral_code;
                
        //         $transactions = Transaction::wwhereRaw("description REGEXP '[[:<:]]{$user->referral_code}[[:>:]]'")
        //             ->get();
                
        //         $Transactions_Drop = Transaction::with('order')
        //             ->whereRaw("description REGEXP '[[:<:]]{$user->referral_code}[[:>:]]'")
        //             ->where('bank', 'DROP')
        //             ->whereHas('order', function ($query) {
        //                 $query->where('reconciled', 1);
        //             })
        //             ->get();
        //         foreach ($transactions as $transaction) {
        //             if ($transaction->type == 'IN') {
        //                 $totalAmount += $transaction->amount;
        //             } elseif ($transaction->type == 'OUT') {
        //                 $totalAmount -= $transaction->amount;
        //             }
        //         }
                
        //         $user->total_amount = $totalAmount;
        //         $user->save();
        //     }
        //     $this->info(' cộng tiền cho user');
    }
}
