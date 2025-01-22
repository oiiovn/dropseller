<?php
namespace App\Imports;

use App\Models\Order; // Đảm bảo rằng model Order trỏ đến bảng order_tiktok
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderTiktokImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        dd($row);
        return new Order([
            'ma_don_hang' => $row['ma_don_hang'],               // "Mã đơn hàng"
            'trang_thai' => $row['trang_thai'],                 // "Trạng thái"
            'ngay_tao_don' => $this->formatDate($row['ngay_tao_don']), // "Ngày tạo đơn"
            'ma_san_pham' => $row['ma_san_pham'],               // "Mã sản phẩm"
            'so_luong' => (int) $row['so_luong'],               // "Số lượng"
            'gia_salework' => (float) $row['gia_salework'],     // "Giá Salework"
            'ten_san_pham' => $row['ten_san_pham'],             // "Tên sản phẩm"
            'gia_san_tmdt' => (float) $row['gia_san_tmdt'],     // "Giá sàn TMDT"
            'doanh_thu_uoc_tinh' => (float) $row['doanh_thu_uoc_tinh'], // "Doanh thu ước tính"
            'chiet_khau' => (float) $row['chiet_khau'],         // "Chiết khấu"
            'khach_tra_truoc' => (float) $row['khach_tra_truoc'], // "Khách trả trước"
            'phi_van_chuyen' => (float) $row['phi_van_chuyen'], // "Phí vận chuyển"
            'ten_khach_hang' => $row['ten_khach_hang'],         // "Tên khách hàng"
            'so_dien_thoai' => $row['so_dien_thoai'],           // "Số điện thoại"
            'ma_van_don' => $row['ma_van_don'],                 // "Mã vận đơn"
            'don_vi_van_chuyen' => $row['don_vi_van_chuyen'],   // "Đơn vị vận chuyển"
            'dia_chi' => $row['dia_chi'],                       // "Địa chỉ"
            'tinh' => $row['tinh'],                             // "Tỉnh"
            'huyen' => $row['huyen'],                           // "Huyện"
            'xa' => $row['xa'],                                 // "Xã"
            'ghi_chu_cua_khach' => $row['ghi_chu_cua_khach'],   // "Ghi chú của khách"
            'shop' => $row['shop'],                             // "Shop"
            'nguoi_tao_don' => $row['nguoi_tao_don'],           // "Người tạo đơn"
            'tien_thu_ho' => (float) $row['tien_thu_ho'],       // "Tiền thu hộ"
            'phi_ship' => (float) $row['phi_ship'],             // "Phí ship"
            'tong_gia_tri' => (float) $row['tong_gia_tri'],     // "Tổng giá trị"
            'xu_ly_don_hang' => $row['xu_ly_don_hang'],         // "Xử lý đơn hàng"
            'thoi_gian_xu_ly' => $this->formatDate($row['thoi_gian_xu_ly']), // "Thời gian xử lý"
            'dong_hang' => $row['dong_hang'],                   // "Đóng hàng"
            'thoi_gian_dong' => $this->formatDate($row['thoi_gian_dong']), // "Thời gian đóng"
            'gui_hang' => $row['gui_hang'],                     // "Gửi hàng"
            'thoi_gian_gui' => $this->formatDate($row['thoi_gian_gui']), // "Thời gian gửi"
            'kho_hang' => $row['kho_hang'],                     // "Kho hàng
            'nguoi_ban_khuyen_mai' => $row['nguoi_ban_khuyen_mai'], // "Người bá"n khuyến mãi"
        ]);
    }

    private function formatDate($value)
    {
        try {
            return \Carbon\Carbon::createFromFormat('H:i:s d/m/Y', $value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null; // Trả về null nếu không thể parse ngày tháng
        }
    }
}
