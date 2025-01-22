<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Pay2sService
{
    public function createTransaction($data)
    {
        $response = Http::post(env('PAY2S_API_ENDPOINT'), [
            'partner_code' => env('PAY2S_PARTNER_CODE'),
            'access_key' => env('PAY2S_ACCESS_KEY'),
            'secret_key' => env('PAY2S_SECRET_KEY'),
            'transaction_data' => $data, // Dữ liệu giao dịch
        ]);

        if ($response->successful()) {
            return $response->json(); // Trả về dữ liệu JSON từ API
        }

        return null; // Trả về null nếu lỗi
    }
}
