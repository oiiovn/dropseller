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
        Schema::table('crm_commissions', function (Blueprint $table) {
            $table->foreignId('commission_rule_id')->nullable()->after('affiliate_id')->constrained('crm_commission_rules')->nullOnDelete();
            $table->string('applied_rule_name')->nullable()->after('commission_amount_jpy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commission_rule_id');
            $table->dropColumn('applied_rule_name');
        });
    }
};
