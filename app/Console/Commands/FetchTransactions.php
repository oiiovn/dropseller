<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
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
        // URL của API
        $url = "https://my.pay2s.vn/userapi/transactions";

        // Secret key và token
        $secretKey = "1ece1f568539eeb7b971578c32a317369defbf0e64b8336124bdc47399a3419e";
        $token = base64_encode($secretKey);

        $requestBody = [
            "bankAccounts" => "008338298888", // Số tài khoản chính xác
            "begin" => "01/03/2025",        // Ngày bắt đầu 
            "end" => "20/11/2029"          // Ngày kết thúc
        ];

        // Gửi request tới API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'pay2s-token' => $token
        ])->post($url, $requestBody);

        if ($response->failed()) {
            $this->error('API fetch failed.');
            return;
        }

        $transactions = $response->json()['transactions'] ?? [];
        foreach ($transactions as $transaction) {
            if (Transaction::where('transaction_id', $transaction['transaction_id'])->exists()) {
                continue; 
            }
            Transaction::create([
                'id' => $transaction['id'],
                'bank' => $transaction['bank'],
                'account_number' => $transaction['account_number'],
                'transaction_date' => $transaction['transaction_date'],
                'transaction_id' => $transaction['transaction_id'],
                'amount' => $transaction['amount'],
                'type' => $transaction['type'],
                'description' => $transaction['description']
            ]);
        
        
            
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
                
                $user->total_amount = $totalAmount;
                $user->save();
            }
            $this->info(' cộng tiền cho user');
    }
}
