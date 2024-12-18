<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TikTokController extends Controller
{
    public function fetchTikTokData()
    {
        $url = 'https://open-api.tiktokglobalshop.com/api/products/search'; // Thay bằng endpoint của TikTok API
        $accessToken = '081128'; // Thay bằng access token của bạn

        try {
            // Gửi yêu cầu tới API TikTok
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($url);

            // Kiểm tra nếu thành công
            if ($response->successful()) {
                $data = $response->json(); // Chuyển kết quả sang JSON
                return view('product.list_product', compact('data')); // Gửi dữ liệu tới view
            } else {
                return response()->json([
                    'message' => 'Lỗi khi gọi API',
                    'status' => $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
