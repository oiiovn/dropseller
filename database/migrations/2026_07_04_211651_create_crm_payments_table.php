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
        Schema::create('crm_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('crm_orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('amount_jpy', 14, 2);
            $table->string('payment_method', 30)->default('bank_transfer');
            $table->date('payment_date');
            $table->string('reference_code')->nullable();
            $table->string('payment_status', 30)->default('received')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_payments');
    }
};
