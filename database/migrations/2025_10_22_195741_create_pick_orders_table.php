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
        Schema::create('pick_orders', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->comment('Mã sản phẩm');
            $table->string('product_name')->comment('Tên sản phẩm');
            $table->integer('quantity_sold')->default(0)->comment('Số lượng bán');
            $table->string('sku')->nullable()->comment('SKU từ Salework');
            $table->text('product_image')->nullable()->comment('Ảnh sản phẩm từ Salework');
            $table->integer('stock')->default(0)->comment('Tồn kho từ Salework');
            $table->string('category')->nullable()->comment('Danh mục từ Salework');
            $table->string('status')->default('pending')->comment('Trạng thái: pending, picked, completed');
            $table->timestamp('picked_at')->nullable()->comment('Thời gian đã nhặt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pick_orders');
    }
};
