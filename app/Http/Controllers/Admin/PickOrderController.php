<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PickOrder;
use App\Models\SaleworkProduct;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PickOrderController extends Controller
{
    /**
     * Hiển thị trang nhặt hàng với 2 tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'pick-order'); // Mặc định là tab nhặt hàng
        
        // Lấy dữ liệu từ database thực tế và sắp xếp theo kệ (bao gồm cả picked và pending)
        $pickOrders = PickOrder::all()->sort(function($a, $b) {
            // Hàm trích xuất ký hiệu kệ từ tên sản phẩm
            $getShelfInfo = function($productName) {
                if (preg_match('/^([A-Za-z]+)(\d+)_/', $productName, $matches)) {
                    return [
                        'letter' => strtoupper($matches[1]),
                        'number' => (int)$matches[2],
                        'sortKey' => strtoupper($matches[1]) . str_pad($matches[2], 5, '0', STR_PAD_LEFT)
                    ];
                }
                return ['letter' => 'ZZZ', 'number' => 9999, 'sortKey' => 'ZZZ99999'];
            };
            
            $shelfA = $getShelfInfo($a->product_name ?? '');
            $shelfB = $getShelfInfo($b->product_name ?? '');
            
            return strcmp($shelfA['sortKey'], $shelfB['sortKey']);
        })->values();
        
        // Dữ liệu cho tab đơn đặt hàng - Lấy từ database hoặc dữ liệu mẫu
        // TODO: Thay thế bằng query thực tế từ database
        $purchaseOrders = collect([
            (object)[
                'id' => 1001,
                'supplier_name' => 'Nhà cung cấp A',
                'product_name' => 'Áo thun nam',
                'quantity' => 100,
                'total_amount' => 5000000,
                'status' => 'ordered',
                'created_at' => now()
            ],
            (object)[
                'id' => 1002,
                'supplier_name' => 'Nhà cung cấp B',
                'product_name' => 'Quần jean',
                'quantity' => 50,
                'total_amount' => 3500000,
                'status' => 'ordered',
                'created_at' => now()->subDays(1)
            ],
        ]);
        
        return view('admin.pick-order.index', compact('activeTab', 'pickOrders', 'purchaseOrders'));
    }
    
    /**
     * Lấy danh sách đơn hàng cần nhặt
     */
    public function getPickOrders(Request $request)
    {
        // Logic lấy danh sách đơn hàng cần nhặt
        $pickOrders = Order::query()
            ->where('status', 'pending') // Ví dụ: lấy đơn pending
            ->paginate(20);
            
        return response()->json($pickOrders);
    }
    
    /**
     * Lấy danh sách đơn đặt hàng
     */
    public function getPurchaseOrders(Request $request)
    {
        // Logic lấy danh sách đơn đặt hàng
        $purchaseOrders = Order::query()
            ->where('status', 'ordered') // Ví dụ: lấy đơn đã đặt hàng
            ->paginate(20);
            
        return response()->json($purchaseOrders);
    }

    /**
     * Upload file Excel và xử lý dữ liệu
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        try {
            $file = $request->file('excel_file');
            
            // Xóa dữ liệu cũ trước khi import
            PickOrder::truncate();
            
            $data = Excel::toArray([], $file);
            
            if (empty($data) || count($data[0]) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel không có dữ liệu hoặc định dạng không đúng'
                ], 400);
            }

            $rows = $data[0];
            $header = array_shift($rows); // Bỏ dòng header
            
            $importedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Kiểm tra định dạng: SKU, Tên sản phẩm, Số lượng bán
                    if (count($row) < 3) {
                        $errors[] = "Dòng " . ($index + 2) . ": Thiếu dữ liệu";
                        continue;
                    }

                    $sku = trim($row[0]);
                    $productName = trim($row[1]);
                    $quantitySold = (int) $row[2];

                    if (empty($sku) || empty($productName)) {
                        $errors[] = "Dòng " . ($index + 2) . ": SKU hoặc tên sản phẩm không được để trống";
                        continue;
                    }

                    // Gọi API Salework trước để lấy thông tin bổ sung
                    $saleworkData = $this->fetchSaleworkDataBySku($sku);
                    
                    // Tạo record mới với dữ liệu đầy đủ từ API
                    $pickOrder = PickOrder::create([
                        'product_code' => $sku, // Lưu SKU vào product_code
                        'product_name' => $productName,
                        'quantity_sold' => $quantitySold,
                        'original_quantity' => $quantitySold, // Lưu số lượng ban đầu
                        'sku' => $sku, // Lưu SKU vào trường sku
                        'product_image' => $saleworkData['image_url'] ?? null,
                        'stock' => $saleworkData['stock'] ?? 0,
                        'category' => $saleworkData['category'] ?? null,
                        'status' => 'pending'
                    ]);
                    
                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Dòng " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Đã thay thế dữ liệu cũ và import thành công {$importedCount} sản phẩm";
            if (!empty($errors)) {
                $message .= ". Lỗi: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " và " . (count($errors) - 5) . " lỗi khác";
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'imported_count' => $importedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Excel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xử lý file Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tất cả sản phẩm từ API Salework (cache 5 phút)
     */
    private function getAllSaleworkProducts()
    {
        return cache()->remember('salework_products', 300, function () {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'client-id' => env('SALEWORK_CLIENT_ID', '1605'),
                        'token' => env('SALEWORK_TOKEN', '+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L'),
                        'Content-Type' => 'application/json'
                    ])
                    ->get('https://salework.net/api/open/stock/v1/product/list');

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'success' && isset($data['data']['products'])) {
                    // Chuyển đổi từ object thành array
                    $products = $data['data']['products'];
                    $productsArray = [];
                    
                    foreach ($products as $code => $product) {
                        $productsArray[] = [
                            'code' => $code,
                            'sku' => $product['code'] ?? $code,
                            'name' => $product['name'] ?? '',
                            'image' => $product['image'] ?? null,
                            'cost' => $product['cost'] ?? 0,
                            'retailPrice' => $product['retailPrice'] ?? 0,
                            'wholesalePrice' => $product['wholesalePrice'] ?? 0,
                            'barcode' => $product['barcode'] ?? '',
                            'stocks' => $product['stocks'] ?? [],
                            'stock' => isset($product['stocks'][0]['value']) ? $product['stocks'][0]['value'] : 0, // Lấy tồn kho từ stocks[0].value
                            'category' => null, // API không có category
                        ];
                    }
                    
                    return $productsArray;
                }
            }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Salework API error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Lấy dữ liệu từ API Salework dựa trên SKU (trước khi lưu database)
     */
    private function fetchSaleworkDataBySku($sku)
    {
        try {
            // Ưu tiên lấy từ database salework_products trước
            $saleworkProduct = SaleworkProduct::where('product_code', $sku)->first();
            
            if ($saleworkProduct) {
                return [
                    'image_url' => $saleworkProduct->image_url,
                    'stock' => $saleworkProduct->stock,
                    'category' => $saleworkProduct->category
                ];
            }
            
            // Nếu không có trong database, fallback về API
            $products = $this->getAllSaleworkProducts();
            
            // Tìm sản phẩm theo SKU
            foreach ($products as $product) {
                if (isset($product['sku']) && $product['sku'] === $sku) {
                    return [
                        'image_url' => $product['image'] ?? null,
                        'stock' => $product['stock'] ?? 0,
                        'category' => $product['category'] ?? null
                    ];
                }
            }

            // Trả về dữ liệu mặc định nếu không tìm thấy
            return [
                'image_url' => null,
                'stock' => 0,
                'category' => null
            ];

        } catch (\Exception $e) {
            Log::warning('Salework API error for SKU ' . $sku . ': ' . $e->getMessage());
            
            // Trả về dữ liệu mặc định khi có lỗi API
            return [
                'image_url' => null,
                'stock' => 0,
                'category' => null
            ];
        }
    }

    /**
     * Đánh dấu sản phẩm đã nhặt
     */
    public function markAsPicked(Request $request, $id)
    {
        try {
            $pickOrder = PickOrder::findOrFail($id);
            $status = $request->input('status', 'picked');
            
            $updateData = [
                'status' => $status
            ];
            
            // Chỉ cập nhật picked_at khi tick vào
            if ($status === 'picked') {
                $updateData['picked_at'] = now();
            } else {
                $updateData['picked_at'] = null;
            }
            
            $pickOrder->update($updateData);

            return response()->json([
                'success' => true,
                'message' => $status === 'picked' ? 'Đã đánh dấu sản phẩm đã nhặt' : 'Đã bỏ đánh dấu sản phẩm'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:0'
            ]);

            $pickOrder = PickOrder::findOrFail($id);
            
            $pickOrder->update([
                'quantity_sold' => $request->quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng thành công',
                'quantity' => $pickOrder->quantity_sold
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật số lượng: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Upload file Excel Salework và lưu vào bảng salework_products
     */
    public function uploadSalework(Request $request)
    {
        // Debug: Log request data
        Log::info('Upload Salework Debug:', [
            'has_file' => $request->hasFile('salework_file'),
            'all_files' => $request->allFiles(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'post_data_keys' => array_keys($request->all()),
            'php_upload_errors' => $_FILES ?? 'No $_FILES'
        ]);
        
        // Kiểm tra file có được upload không
        if (!$request->hasFile('salework_file')) {
            Log::error('No file uploaded - possibly exceeds PHP limits');
            return response()->json([
                'success' => false,
                'message' => 'File không được upload. File size: 2.8MB (hợp lệ). Nguyên nhân có thể: 1) File Excel bị lỗi định dạng 2) Browser chặn upload 3) Network timeout. Giải pháp: 1) Mở lại file Excel và Save As 2) Clear browser cache 3) Thử browser khác'
            ], 400);
        }
        
        try {
            $request->validate([
                'salework_file' => 'required|file|mimes:xlsx,xls|max:4096' // 4MB max (PHP limit)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        }

        try {
            $file = $request->file('salework_file');
            $clearExisting = $request->has('clear_existing') && $request->input('clear_existing') == 'on';

            // Xóa dữ liệu cũ nếu được yêu cầu
            if ($clearExisting) {
                SaleworkProduct::truncate();
            }

            // Đọc file Excel
            $data = Excel::toArray([], $file);
            
            if (empty($data) || count($data[0]) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel không có dữ liệu hoặc định dạng không đúng'
                ], 400);
            }

            $rows = $data[0];
            $header = array_shift($rows); // Bỏ dòng header
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Kiểm tra dữ liệu cần thiết
                    if (empty($row[0])) { // MÃ SẢN PHẨM
                        continue;
                    }

                    $productData = [
                        'product_code' => trim($row[0]), // MÃ SẢN PHẨM
                        'product_name' => trim($row[1] ?? ''), // TÊN SẢN PHẨM
                        'unit' => trim($row[2] ?? ''), // ĐƠN VỊ TÍNH
                        'tax_rate' => floatval($row[3] ?? 0), // THUẾ GTGT
                        'import_price' => floatval($row[4] ?? 0), // GIÁ NHẬP
                        'wholesale_price' => floatval($row[5] ?? 0), // GIÁ BÁN BUÔN
                        'retail_price' => floatval($row[6] ?? 0), // GIÁ BÁN LẺ
                        'barcode' => trim($row[7] ?? ''), // BARCODE
                        'stock' => intval($row[8] ?? 0), // TỒN KHO
                        'reserved_stock' => intval($row[9] ?? 0), // GIỮ HÀNG
                        'image_url' => trim($row[10] ?? ''), // LINK ẢNH
                        'category' => trim($row[11] ?? ''), // DANH MỤC
                    ];

                    // Tạo hoặc cập nhật sản phẩm
                    SaleworkProduct::updateOrCreate(
                        ['product_code' => $productData['product_code']],
                        $productData
                    );

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Dòng " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import thành công {$imported} sản phẩm từ Salework";
            if (!empty($errors)) {
                $message .= ". Lỗi: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " và " . (count($errors) - 5) . " lỗi khác";
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Upload Salework error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xử lý file: ' . $e->getMessage()
            ], 500);
        }
    }
}

