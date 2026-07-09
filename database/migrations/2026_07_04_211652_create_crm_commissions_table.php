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
        Schema::create('crm_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('crm_orders')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount_jpy', 14, 2)->default(0);
            $table->string('approval_status', 30)->default('pending')->index();
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->date('approved_at')->nullable();
            $table->date('paid_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_commissions');
    }
};
