<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->string('payment_type', 30)->default('partial')->after('amount_jpy');
            $table->foreignId('recorded_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn('payment_type');
        });
    }
};
