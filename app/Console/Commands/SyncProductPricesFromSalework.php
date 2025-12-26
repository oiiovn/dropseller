<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Exception;

class SyncProductPricesFromSalework extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-salework';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ giá vốn sản phẩm từ API Salework';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu đồng bộ giá vốn từ Salework...');

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
                $this->error('❌ Không thể kết nối tới API Salework.');
                return 1;
            }

            $data = json_decode($response, true);

            if (!isset($data['status']) || $data['status'] !== 'success') {
                $this->error('❌ API trả về lỗi: ' . ($data['message'] ?? 'Không xác định.'));
                return 1;
            }

            $products = $data['data']['products'] ?? [];

            if (empty($products)) {
                $this->warn('⚠️  Không có sản phẩm nào được trả về từ API.');
                return 0;
            }

            $this->info('📦 Tìm thấy ' . count($products) . ' sản phẩm từ API.');

            $skipped = 0;
            $updated = 0;
            $inserted = 0;

            $progressBar = $this->output->createProgressBar(count($products));
            $progressBar->start();

            foreach ($products as $productData) {
                // Lấy SKU và giá vốn từ API
                $sku = $productData['code'] ?? $productData['sku'] ?? null;
                $price = $productData['price'] ?? $productData['cost'] ?? null;

                if (!$sku || !$price) {
                    $progressBar->advance();
                    continue; // Bỏ qua nếu thiếu thông tin
                }

                // Kiểm tra sản phẩm có tồn tại không
                $product = Product::where('sku', $sku)->first();

                if ($product) {
                    // Nếu giá giống nhau thì bỏ qua
                    if ($product->price == $price) {
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    // Cập nhật giá
                    $product->update(['price' => $price]);
                    $updated++;
                } else {
                    // Thêm mới sản phẩm
                    Product::create([
                        'sku' => $sku,
                        'price' => $price,
                    ]);
                    $inserted++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Hiển thị kết quả
            $this->info('✅ Đồng bộ hoàn tất!');
            $this->table(
                ['Loại', 'Số lượng'],
                [
                    ['Thêm mới', $inserted],
                    ['Cập nhật', $updated],
                    ['Bỏ qua (giá không đổi)', $skipped],
                    ['Tổng cộng', count($products)],
                ]
            );

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Có lỗi xảy ra: ' . $e->getMessage());
            return 1;
        }
    }
}
