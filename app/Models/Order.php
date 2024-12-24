<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Các trường cho phép gán hàng loạt
    protected $fillable = [
        'order_code',             
        'status',                   
        'createdorder_at',        
        'product_code',             // ma_san_pham
        'quantity',                 // so_luong
        'salework_price',           // gia_salework
        'product_name', 
        'soluongban_tiktok',            // ten_san_pham
        'marketplace_price',        // gia_san_tmdt
        'estimated_revenue',        // doanh_thu_uoc_tinh
        'discount',                 // chiet_khau
        'prepaid_amount',           // khach_tra_truoc
        'shipping_fee',             // phi_van_chuyen
        'customer_name',            // ten_khach_hang
        'phone_number',             // so_dien_thoai
        'shipment_code',            // ma_van_don
        'carrier',                  // don_vi_van_chuyen
        'address',                  // dia_chi
        'province',                 // tinh
        'district',                 // huyen
        'ward',                     // xa
        'customer_note',            // ghi_chu_cua_khach
        'shop_name',                // shop
        'creator',                  // nguoi_tao_don
        'cod_amount',               // tien_thu_ho
        'shipping_fee_collected',   // phi_ship
        'total_value',              // tong_gia_tri
        'order_processing_status',  // xu_ly_don_hang
        'processing_time',          // thoi_gian_xu_ly
        'packed',                   // dong_hang
        'packing_time',             // thoi_gian_dong
        'shipped',                  // gui_hang
        'shipping_time',            // thoi_gian_gui
        'warehouse',                // kho_hang
        'promotion_by_seller',      // nguoi_ban_khuyen_mai
    ];
}
