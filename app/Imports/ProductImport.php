<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
     * Hàm nhận từng dòng dữ liệu từ file Excel và lưu vào bảng.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $skipped = [];   // SKU bỏ qua
    public $updated = [];   // SKU được cập nhật
    public $inserted = [];  // SKU được thêm mới

    public function model(array $row)
    {
        $product = Product::where('sku', $row[0])->first();

        if ($product) {
            if ($product->price == $row[1]) {
                $this->skipped[] = $row[0]; // SKU bị bỏ qua
                return null;
            }

            $product->update(['price' => $row[1]]);
            $this->updated[] = $row[0]; // SKU được cập nhật
            return null;
        }

        $this->inserted[] = $row[0]; // SKU được thêm mới
        return new Product([
            'sku' => $row[0],
            'price' => $row[1],
        ]);
    }
}
