<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_shop', function (Blueprint $table) {
            $table->id();
            $table->string('shop_id');
            $table->unsignedBigInteger('program_id');

            // Tạo unique theo cặp shop_id + program_id
            $table->unique(['shop_id', 'program_id']);
            $table->string('status_program')->default('chưa triển khai'); // VD: đang chạy, tạm dừng...
            $table->string('status_payment')->default('chưa thanh toán'); // VD: đã thanh toán, chờ duyệt...
            $table->float('total_payment'); // tổng tiền thanh toán
            $table->string('payment_code')->nullable(); // mã thanh toán
            $table->unsignedBigInteger('confirmer')->nullable(); // người xác nhận

            $table->timestamps();

            // Quan hệ (nếu có bảng `shops`, `programs`, `users`)
            $table->foreign('shop_id')->references('shop_id')->on('shops_name')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('confirmer')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_shop');
    }
};
