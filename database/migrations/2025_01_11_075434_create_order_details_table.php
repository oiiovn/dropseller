<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id(); // ID tự động tăng
            $table->unsignedBigInteger('order_id'); // ID của đơn hàng (liên kết với bảng orders)
            $table->string('shop_name'); // Tên shop
            $table->string('sku'); // Mã SKU
            $table->string('product_name'); // Tên sản phẩm
            $table->integer('quantity'); // Số lượng
            $table->decimal('unit_cost', 15, 2); // Giá vốn (giá trên 1 đơn vị)
            $table->decimal('total_cost', 15, 2); // Tổng giá vốn
            $table->timestamps(); // Thời gian tạo và cập nhật

            // Thiết lập khóa ngoại nếu bạn có bảng orders
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
