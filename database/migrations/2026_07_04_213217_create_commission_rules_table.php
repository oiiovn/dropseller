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
        Schema::create('crm_commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_category')->nullable()->index();
            $table->decimal('min_order_amount_jpy', 14, 2)->nullable();
            $table->decimal('max_order_amount_jpy', 14, 2)->nullable();
            $table->decimal('min_affiliate_sales_jpy', 14, 2)->nullable();
            $table->decimal('max_affiliate_sales_jpy', 14, 2)->nullable();
            $table->decimal('rate_percent', 5, 2);
            $table->unsignedInteger('priority')->default(100);
            $table->boolean('is_active')->default(true)->index();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_commission_rules');
    }
};
