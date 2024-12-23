<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTiktokTable extends Migration
{
    public function up()
    {
        Schema::create('order_tiktok', function (Blueprint $table) {
            $table->id(); // ID tự động tăng
            $table->string('ma_don_hang')->unique();
            $table->string('trang_thai')->nullable();
            $table->date('ngay_tao_don')->nullable();
            $table->string('ma_san_pham')->nullable();
            $table->integer('so_luong')->nullable();
            $table->decimal('gia_salework', 15, 2)->nullable();
            $table->string('ten_san_pham')->nullable();
            $table->decimal('gia_san_tmdt', 15, 2)->nullable();
            $table->decimal('doanh_thu_uoc_tinh', 15, 2)->nullable();
            $table->decimal('chiet_khau', 15, 2)->nullable();
            $table->decimal('khach_tra_truoc', 15, 2)->nullable();
            $table->decimal('phi_van_chuyen', 15, 2)->nullable();
            $table->string('ten_khach_hang')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('ma_van_don')->nullable();
            $table->string('don_vi_van_chuyen')->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('tinh')->nullable();
            $table->string('huyen')->nullable();
            $table->string('xa')->nullable();
            $table->text('ghi_chu_cua_khach')->nullable();
            $table->string('shop')->nullable();
            $table->string('nguoi_tao_don')->nullable();
            $table->decimal('tien_thu_ho', 15, 2)->nullable();
            $table->decimal('phi_ship', 15, 2)->nullable();
            $table->decimal('tong_gia_tri', 15, 2)->nullable();
            $table->string('xu_ly_don_hang')->nullable();
            $table->timestamp('thoi_gian_xu_ly')->nullable();
            $table->string('dong_hang')->nullable();
            $table->timestamp('thoi_gian_dong')->nullable();
            $table->string('gui_hang')->nullable();
            $table->timestamp('thoi_gian_gui')->nullable();
            $table->string('kho_hang')->nullable();
            $table->string('nguoi_ban_khuyen_mai')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_tiktok');
    }
}
