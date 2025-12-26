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
        Schema::create('ADS', function (Blueprint $table) {
            $table->id(); // ID tự động tăng
            $table->string('invoice_id')->unique(); // Mã hóa đơn
            $table->string('shop_id'); // Khóa ngoại đến shops
            $table->string('date_range');
            $table->integer('amount'); // VAT
            $table->integer('vat'); // VAT
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng tiền (15 số, 2 số thập phân)
            $table->string('payment_status')->default('pending'); // Trạng thái thanh toán
            $table->string('payment_code')->nullable(); // Mã thanh toán
            $table->timestamps(); // Tự động tạo created_at & updated_at
            $table->foreign('shop_id')->references('shop_id')->on('shops_name')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ADS');
    }
};
