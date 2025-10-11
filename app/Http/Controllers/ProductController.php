<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use  App\Models\Product;

class ProductController extends Controller
{
    function Getproduct()
    {
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
        $platforms = $request->input('platform', '');

        $payload = [
            "time_start" => $timeStart,
            "time_end" => $timeEnd,
            "platform" => $platforms,
        ];

        $response = $this->sendApiRequest($apiUrl, $payload, $clientId, $token);
        if (!$response) {
            return back()->with('error', 'Không thể kết nối tới API.');
        }

        $data = json_decode($response, true);
        if (!isset($data['status']) || $data['status'] !== 'success') {
            return back()->with('error', 'API trả về lỗi: ' . ($data['message'] ?? 'Không xác định.'));
        }

        $productReport = $data['data']['product_report'][$platforms] ?? [];

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
                    $productFromDb = Product::where('sku', $product['code'])->first();
                    $filteredProducts[] = [
                        'code' => $product['code'],
                        'name' => $product['name'],
                        'amount' => $product['amount'],
                        'api_price' => $product['revenue'], // Giá từ API
                        'image' => $product['image'],
                        'db_price' => $productFromDb ? $productFromDb->price : 'Không tìm thấy', // Giá từ bảng products
                    ];
                }
            }
        }
        $totalAmounts = array_sum(array_column($filteredProducts, 'amount'));

        $filterDate = $startDate . ' - ' . $endDate;

        return view('product.report', compact('filteredProducts', 'filterDate', 'startDate', 'endDate', 'shopId', 'platforms', 'totalAmounts'));
    }

    public function lish(Request $request)
    {

        return view('product.report');
    }
    public function getshopid(Request $request)
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
        $platforms = $request->input('platform', '');

        $payload = [
            "time_start" => $timeStart,
            "time_end" => $timeEnd,
            "platform" => $platforms,
        ];

        $response = $this->sendApiRequest($apiUrl, $payload, $clientId, $token);
        if (!$response) {
            return back()->with('error', 'Không thể kết nối tới API.');
        }

        $data = json_decode($response, true);
        if (!isset($data['status']) || $data['status'] !== 'success') {
            return back()->with('error', 'API trả về lỗi: ' . ($data['message'] ?? 'Không xác định.'));
        }

        $Shop_id = $data['data']['product_report'][$platforms] ?? [];
        $Shop_id = array_column($Shop_id, 'shopId');
        return view('product.report', compact('Shop_id'));
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        $import = new ProductImport;
        Excel::import($import, $request->file('file'));
        return redirect()->back()->with([
            'success' => 'Import thành công!',
            'skipped' => $import->skipped,
            'updated' => $import->updated,
            'inserted' => $import->inserted,
        ]);
    }

    /**
     * Đồng bộ giá vốn sản phẩm từ API Salework
     */
    public function syncFromSalework()
    {
        try {
            $apiUrl = "https://salework.net/api/open/stock/v1/product/list";
            $clientId = "1605";
            $token = "+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L";

            // Gọi API để lấy danh sách sản phẩm
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "client-id: $clientId",
                "token: $token"
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (!$response || $httpCode !== 200) {
                return redirect()->back()->with('error', 'Không thể kết nối tới API Salework.');
            }

            $data = json_decode($response, true);

            if (!isset($data['status']) || $data['status'] !== 'success') {
                return redirect()->back()->with('error', 'API trả về lỗi: ' . ($data['message'] ?? 'Không xác định.'));
            }

            $products = $data['data']['products'] ?? [];

            if (empty($products)) {
                return redirect()->back()->with('error', 'Không có sản phẩm nào được trả về từ API.');
            }

            $skipped = [];
            $updated = [];
            $inserted = [];

            foreach ($products as $productData) {
                // Lấy SKU và giá vốn từ API
                // Cấu trúc dữ liệu có thể khác nhau, cần điều chỉnh theo response thực tế
                $sku = $productData['code'] ?? $productData['sku'] ?? null;
                $price = $productData['price'] ?? $productData['cost'] ?? null;

                if (!$sku || !$price) {
                    continue; // Bỏ qua nếu thiếu thông tin
                }

                // Kiểm tra sản phẩm có tồn tại không
                $product = Product::where('sku', $sku)->first();

                if ($product) {
                    // Nếu giá giống nhau thì bỏ qua
                    if ($product->price == $price) {
                        $skipped[] = $sku;
                        continue;
                    }

                    // Cập nhật giá
                    $product->update(['price' => $price]);
                    $updated[] = $sku;
                } else {
                    // Thêm mới sản phẩm
                    Product::create([
                        'sku' => $sku,
                        'price' => $price,
                    ]);
                    $inserted[] = $sku;
                }
            }

            return redirect()->back()->with([
                'success' => 'Đồng bộ giá vốn từ Salework thành công!',
                'skipped' => $skipped,
                'updated' => $updated,
                'inserted' => $inserted,
            ]);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

}
