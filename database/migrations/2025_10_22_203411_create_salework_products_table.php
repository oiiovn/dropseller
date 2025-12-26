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
        Schema::create('salework_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique(); // MÃ SẢN PHẨM
            $table->string('product_name'); // TÊN SẢN PHẨM
            $table->string('unit')->nullable(); // ĐƠN VỊ TÍNH
            $table->decimal('tax_rate', 5, 2)->default(0); // THUẾ GTGT
            $table->decimal('import_price', 15, 2)->default(0); // GIÁ NHẬP
            $table->decimal('wholesale_price', 15, 2)->default(0); // GIÁ BÁN BUÔN
            $table->decimal('retail_price', 15, 2)->default(0); // GIÁ BÁN LẺ
            $table->string('barcode')->nullable(); // BARCODE
            $table->integer('stock')->default(0); // TỒN KHO
            $table->integer('reserved_stock')->default(0); // GIỮ HÀNG
            $table->text('image_url')->nullable(); // LINK ẢNH
            $table->string('category')->nullable(); // DANH MỤC
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salework_products');
    }
};
