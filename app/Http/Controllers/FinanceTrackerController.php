<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory; // nhớ thêm dòng này vào đầu controller


class FinanceTrackerController extends Controller
{
    public function create()
    {
        return view('finance_tracker.income_expense_create');
    }

    // Trợ lý AI gợi ý phiếu chi
    public function aiSuggest(Request $request)
    {
        $input = $request->input('prompt');

        if (!$input) {
            return response()->json(['error' => 'Nội dung không được để trống.']);
        }

        // Gửi API GPT
        $apiKey = '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';
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $postData = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => ",Hãy chọn trong các category bạn đã biết. Không được tạo mới.
            ,Không để trống thường nào ,Bạn là trợ lý kế toán. Hãy phân tích câu nhập và trả về JSON với định dạng và phân tích theo hướng nếu như số tiền nhỏ nhỏ thì thường là tiền mặt, còn số tiền lớn thì thì thường là chuyển khoản, người chi thường là tên nhân viên trong công ty, :
            {
            \"payer\": \"Tên người chi\",
            \"amount\": \"Số tiền (chỉ số, không có ký tự, định nghĩa theo tiền việt hoặc cách nói thông thường)\",
            \"category\": \"Loại chi phù hợp (ví dụ: Phí vận chuyển, Mặt bằng, Lương... Không được để trống, tiền nhà cũng là mặt bằng, loại tiền về thức ăn uống đều là ) \",
            \"note\": \"Ghi chú/nội dung chi, trả nội dung phải có đầy đủ ý nghĩa\",
            \"payment_method\": \"Hình thức thanh toán (Tiền mặt, Chuyển khoản, Ví điện tử, Thẻ tín dụng)\"
            }

            LƯU Ý: Trả về đúng JSON, không giải thích thêm."],
                ["role" => "user", "content" => $input],
            ],
            "temperature" => 0.2,
            "max_tokens" => 500,
        ];

        // Gọi OpenAI
        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json(['error' => curl_error($ch)]);
        }

        curl_close($ch);

        $result = json_decode($response, true);
        $aiContent = $result['choices'][0]['message']['content'] ?? '{}';

        // Parse JSON từ AI
        $suggestData = json_decode($aiContent, true);

        return response()->json([
            'data' => $suggestData
        ]);
    }
}
