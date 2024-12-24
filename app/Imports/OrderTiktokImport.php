<?php
namespace App\Imports;

use App\Models\OrderTiktok;
use Maatwebsite\Excel\Concerns\ToModel;

class OrderTiktokImport implements ToModel
{
    public function model(array $row)
    {
        return new OrderTiktok([
            'ma_don_hang' => $row[0],
            'trang_thai' => $row[1],
            'ngay_tao_don' => $row[2],
            'ma_san_pham' => $row[3],
            'so_luong' => $row[4],
            'gia_salework' => $row[5],
            'ten_san_pham' => $row[6],
            'gia_san_tmdt' => $row[7],
            'doanh_thu_uoc_tinh' => $row[8],
            'chiet_khau' => $row[9],
            'khach_tra_truoc' => $row[10],
            'phi_van_chuyen' => $row[11],
            'ten_khach_hang' => $row[12],
            'so_dien_thoai' => $row[13],
            'ma_van_don' => $row[14],
            'don_vi_van_chuyen' => $row[15],
            'dia_chi' => $row[16],
            'tinh' => $row[17],
            'huyen' => $row[18],
            'xa' => $row[19],   
            'ghi_chu_cua_khach' => $row[20],
            'shop' => $row[21],
            'nguoi_tao_don' => $row[22],
            'tien_thu_ho' => $row[23],
            'phi_ship' => $row[24],
            'tong_gia_tri' => $row[25],
            'xu_ly_don_hang' => $row[26],
            'thoi_gian_xu_ly' => $row[27],
            'dong_hang' => $row[28],
            'thoi_gian_dong' => $row[29],
            'gui_hang' => $row[30],
            'thoi_gian_gui' => $row[31],
            'kho_hang' => $row[32],
            'nguoi_ban_khuyen_mai' => $row[33],
        ]);
    }
}
