<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_change', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->enum('type', ['deposit', 'withdraw', 'order', 'refund', 'ads', 'product_fee', 'monthly']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('transaction_code')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // 🔹 Ngăn tạo trùng một transaction
            $table->unique(['reference_type', 'reference_id']);
            // 🔹 Index hỗ trợ truy vấn dồn chỉnh nhanh hơn
            $table->index(['user_id', 'created_at', 'id']);
        });        
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_histories');
    }
};

