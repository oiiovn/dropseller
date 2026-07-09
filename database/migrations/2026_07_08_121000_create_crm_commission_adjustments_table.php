<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_commission_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('commission_id')->nullable()->constrained('crm_commissions')->nullOnDelete();
            $table->decimal('amount_jpy', 14, 2);
            $table->string('reason', 255);
            $table->string('wallet_status', 30)->default('credited');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_commission_adjustments');
    }
};
