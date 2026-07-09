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
        Schema::create('crm_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_status', 30)->default('new')->index();
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->string('debt_status', 30)->default('no_debt')->index();
            $table->decimal('subtotal_jpy', 14, 2)->default(0);
            $table->decimal('discount_jpy', 14, 2)->default(0);
            $table->decimal('shipping_fee_jpy', 14, 2)->default(0);
            $table->decimal('total_amount_jpy', 14, 2)->default(0);
            $table->decimal('total_cost_jpy', 14, 2)->default(0);
            $table->decimal('total_profit_jpy', 14, 2)->default(0);
            $table->date('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_orders');
    }
};
