<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
    
            $table->string('order_code')->nullable();
            $table->index('order_code'); // ✅ Cần thiết cho khóa ngoại string
            $table->foreign('order_code')->references('order_code')->on('orders')->onDelete('set null');
            $table->string('shop_id'); // kiểu string do ID nền tảng lớn
            $table->date('ngay');
            $table->json('sku'); // Danh sách sản phẩm
            $table->unsignedBigInteger('tong_tien')->default(0);
            $table->string('payment_status')->default('Chưa thanh toán'); // Trạng thái thanh toán
            $table->string('transaction_id')->nullable(); // Mã giao dịch nếu có
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_orders');
    }
};
