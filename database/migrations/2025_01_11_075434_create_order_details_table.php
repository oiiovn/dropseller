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
            $table->string('status_program')->default('chưa triển khai');
            $table->string('status_payment')->default('chưa thanh toán');
            $table->string('payment_code')->nullable();
            $table->unsignedBigInteger('confirmer')->nullable();
            $table->timestamps();

            // 🔥 Đảm bảo MySQL dùng InnoDB (hỗ trợ khóa ngoại)
            $table->engine = 'InnoDB';

            // ✅ Thêm khóa ngoại
            $table->foreign('shop_id')->references('shop_id')->on('shops')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('confirmer')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_shop');
    }
};
