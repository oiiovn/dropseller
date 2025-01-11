<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class ProductReportController extends Controller
{
    public function fetchProductReport(Request $request)
    {
        $apiUrl = "https://salework.net/api/open/stock/v1/report/product";
        $clientId = "1605";
        $token = "+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L";

        try {
            // Lấy thông tin từ yêu cầu hoặc sử dụng giá trị mặc định
            $timeStart = strtotime($request->input('start_date', '2025-01-01')) * 1000;
            $timeEnd = strtotime($request->input('end_date', '2025-01-10')) * 1000;
            $platform = $request->input('platform', 'Tiktok');

            // Kiểm tra ngày hợp lệ
            if ($timeStart > $timeEnd) {
                return back()->with('error', 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.');
            }

            // Chuẩn bị payload
            $payload = [
                "time_start" => $timeStart,
                "time_end" => $timeEnd,
                "platform" => $platform,
            ];

            // Gửi yêu cầu đến API
            $response = $this->sendApiRequest($apiUrl, $payload, $clientId, $token);

            // Xử lý phản hồi từ API
            $data = json_decode($response, true);

            if (!$data || $data['status'] !== 'success') {
                $errorMessage = $data['message'] ?? 'Lỗi không xác định từ API.';
                return back()->with('error', "API trả về lỗi: $errorMessage");
            }

            // Lấy dữ liệu báo cáo sản phẩm từ API
            $productReport = $data['data']['product_report'][$platform] ?? [];

            return view('product.report', compact('productReport'));

        } catch (Exception $e) {
            // Xử lý ngoại lệ
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Gửi yêu cầu API
     * 
     * @param string $url
     * @param array $payload
     * @param string $clientId
     * @param string $token
     * @return string|null
     */
    private function sendApiRequest($url, $payload, $clientId, $token)
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "client-id: $clientId",
                "token: $token"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception('CURL Error: ' . curl_error($ch));
            }

            curl_close($ch);

            return $response;

        } catch (Exception $e) {
            // Ghi log lỗi nếu cần
            \Log::error('Error during API request: ' . $e->getMessage());
            return null;
        }
    }
}
