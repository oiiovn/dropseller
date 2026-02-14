<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_monthly_income_id')->constrained('debt_monthly_incomes')->cascadeOnDelete();
            $table->foreignId('debt_creditor_id')->constrained('debt_creditors')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->decimal('percent_applied', 5, 2)->nullable();
            $table->string('transaction_code', 100)->unique()->comment('Mã giao dịch đối chiếu khi chuyển khoản');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('bank_transaction_ref', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_distributions');
    }
};
