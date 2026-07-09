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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('full_name');
            $table->string('phone', 30)->unique();
            $table->string('email')->nullable()->index();
            $table->string('area')->nullable()->index();
            $table->string('status', 30)->default('active')->index();
            $table->unsignedInteger('total_referred_customers')->default(0);
            $table->unsignedInteger('successful_orders')->default(0);
            $table->decimal('gross_sales_jpy', 14, 2)->default(0);
            $table->decimal('total_commission_jpy', 14, 2)->default(0);
            $table->decimal('paid_commission_jpy', 14, 2)->default(0);
            $table->decimal('pending_commission_jpy', 14, 2)->default(0);
            $table->date('joined_at')->nullable();
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
        Schema::dropIfExists('affiliates');
    }
};
