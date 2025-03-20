<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsAndFetchLogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tạo bảng transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // ID giao dịch (khóa chính)
            $table->string('bank')->nullable(); // Ngân hàng
            $table->string('account_number')->nullable(); // Số tài khoản
            $table->date('transaction_date')->nullable(); // Ngày giao dịch
            $table->string('transaction_id')->nullable(); // Mã giao dịch
            $table->decimal('amount', 15, 2)->nullable(); // Số tiền
            $table->string('type')->nullable(); // Loại giao dịch (IN/OUT)
            $table->text('description')->nullable(); // Mô tả giao dịch
            $table->timestamps(); // created_at và updated_at
        });

        // Tạo bảng fetch_logs
        Schema::create('fetch_logs', function (Blueprint $table) {
            $table->id(); // ID tự tăng
            $table->dateTime('last_fetch_date'); // Ngày lấy dữ liệu lần cuối
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Xóa bảng nếu rollback
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('fetch_logs');
    }
}
