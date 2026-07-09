<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->decimal('discount_jpy', 14, 2)->default(0)->after('amount_jpy');
        });
    }

    public function down(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->dropColumn('discount_jpy');
        });
    }
};
