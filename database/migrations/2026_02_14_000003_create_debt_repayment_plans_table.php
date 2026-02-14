<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_repayment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_creditor_id')->constrained('debt_creditors')->cascadeOnDelete();
            $table->decimal('monthly_percent', 5, 2)->comment('% tổng thu nhập tháng');
            $table->unsignedTinyInteger('pay_day_of_month')->comment('Ngày trong tháng nhận tiền (1-31)');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_repayment_plans');
    }
};
