<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_commission_rule_affiliate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_rule_id')->constrained('crm_commission_rules')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['commission_rule_id', 'affiliate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_commission_rule_affiliate');
    }
};
