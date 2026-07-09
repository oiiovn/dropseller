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
        Schema::create('crm_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('crm_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('crm_products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price_jpy', 14, 2)->default(0);
            $table->decimal('unit_cost_jpy', 14, 2)->default(0);
            $table->decimal('line_total_jpy', 14, 2)->default(0);
            $table->decimal('line_profit_jpy', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_order_items');
    }
};
