<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;


class ProductController extends Controller
{
   function Getproduct(){
    return view('product.list_product');
   }
   public function fetchProductReport(Request $request)
   {
       $apiUrl = "https://salework.net/api/open/stock/v1/report/product";
       $clientId = "1605";
       $token = "+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L";
       $startDate = $request->input('start_date');
       $endDate = $request->input('end_date');
       if (!$startDate || !$endDate) {
           return back()->with('error', 'Ngày bắt đầu và ngày kết thúc là bắt buộc.');
       }
       $timeStart = Carbon::parse($startDate . ' 00:00:00', 'Asia/Ho_Chi_Minh')->timestamp * 1000;
       $timeEnd = Carbon::parse($endDate . ' 23:59:59', 'Asia/Ho_Chi_Minh')->timestamp * 1000;
       $payload = [
           "time_start" => $timeStart,
           "time_end" => $timeEnd,
           "platform" => $request->input('platform', ''),
       ];
       $response = $this->sendApiRequest($apiUrl, $payload, $clientId, $token);
       if (!$response) {
           return back()->with('error', 'Không thể kết nối tới API.');
       }
       $data = json_decode($response, true);
       if (!isset($data['status']) || $data['status'] !== 'success') {
           return back()->with('error', 'API trả về lỗi: ' . ($data['message'] ?? 'Không xác định.'));
       }
       $productReport = $data['data']['product_report'][$request->input('platform')] ?? [];
       $shopId = $request->input('shop_id');
       if ($shopId) {
           $productReport = array_filter($productReport, function ($shop) use ($shopId) {
               return isset($shop['shopId']) && $shop['shopId'] == $shopId;
           });
       }
       $filteredProducts = [];
       foreach ($productReport as $shop) {
           if (isset($shop['products']) && is_array($shop['products'])) {
               foreach ($shop['products'] as $product) {
                   $filteredProducts[] = $product;
               }
           }
       }
       $filterDate = $startDate . ' - ' . $endDate;
    //    return response()->json($filteredProducts);
       // Trả về view với dữ liệu sản phẩm đã lọc
       return view('product.report', compact('filteredProducts', 'filterDate'));
   }
   
   
   /**
    * Hàm gửi yêu cầu API
    */
   private function sendApiRequest($url, $payload, $clientId, $token)
   {
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
       curl_close($ch);
   
       return $response;
   }
   public function lish(Request $request)
   {
       return view('product.report');
   }
   
}
