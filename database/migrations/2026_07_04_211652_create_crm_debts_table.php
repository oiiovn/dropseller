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
        Schema::create('crm_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('crm_orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('total_payable_jpy', 14, 2)->default(0);
            $table->decimal('total_paid_jpy', 14, 2)->default(0);
            $table->decimal('outstanding_jpy', 14, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('debt_status', 30)->default('unpaid')->index();
            $table->date('last_collected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_debts');
    }
};
