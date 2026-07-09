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
        Schema::create('crm_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('crm_orders')->cascadeOnDelete();
            $table->string('delivery_address');
            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivery_status', 30)->default('pending')->index();
            $table->decimal('shipping_fee_jpy', 14, 2)->default(0);
            $table->dateTime('delivered_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_deliveries');
    }
};
