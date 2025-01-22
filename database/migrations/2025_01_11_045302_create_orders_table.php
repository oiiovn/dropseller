<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // Mã đơn hàng
            $table->timestamp('export_date'); // Ngày xuất bill
            $table->string('filter_date'); // Ngày lọc
            $table->string('shop_name'); // Tên shop
            $table->integer('total_products'); // Tổng số sản phẩm
            $table->decimal('total_dropship', 15, 2); // Tổng tiền dropship
            $table->decimal('total_bill', 15, 2); // Tổng bill
            $table->string('payment_status')->default('pending'); // Trạng thái thanh toán
            $table->string('payment_code')->nullable(); // Mã thanh toán
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
