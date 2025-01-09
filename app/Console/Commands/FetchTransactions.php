<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

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

        // Body request
        $requestBody = [
            "bankAccounts" => "62886838888",
            "begin" => "12/12/2024",
            "end" => date('Y-m-d') // Ngày hiện tại
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

        // Lấy dữ liệu từ API
        $transactions = $response->json()['transactions'] ?? [];

        // Lưu dữ liệu vào cơ sở dữ liệu
        foreach ($transactions as $transaction) {
            Transaction::updateOrCreate(
                ['id' => $transaction['id']], // Dùng `id` làm khóa chính
                [
                    'bank' => $transaction['bank'],
                    'account_number' => $transaction['account_number'],
                    'transaction_date' => $transaction['transaction_date'],
                    'transaction_id' => $transaction['transaction_id'],
                    'amount' => $transaction['amount'],
                    'type' => $transaction['type'],
                    'description' => $transaction['description']
                ]
            );
        }

        $this->info('Transactions fetched and stored successfully.');
    }
}
