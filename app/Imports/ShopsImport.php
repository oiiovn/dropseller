<?php
namespace App\Imports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\ToModel;

class ShopsImport implements ToModel
{
    /**
     * Map dữ liệu từ file Excel vào model Shop
     */
    public function model(array $row)
    {
        // Kiểm tra nếu dữ liệu không hợp lệ
        if (empty($row[0]) || empty($row[1])) {
            return null; // Bỏ qua dòng nếu thiếu shop_id hoặc shop_name
        }

        // Trả về đối tượng Shop để lưu
        return new Shop([
            'shop_id' => $row[0],  // Lấy giá trị shop_id từ cột 0
            'shop_name' => $row[1], // Lấy giá trị shop_name từ cột 1
        ]);
    }
}
