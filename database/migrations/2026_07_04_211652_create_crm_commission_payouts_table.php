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
        Schema::create('crm_commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('crm_commissions')->cascadeOnDelete();
            $table->decimal('amount_jpy', 14, 2);
            $table->date('paid_at');
            $table->string('payment_method', 30)->default('bank_transfer');
            $table->string('reference_code')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_commission_payouts');
    }
};
