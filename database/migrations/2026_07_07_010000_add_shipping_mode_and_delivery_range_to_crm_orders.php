<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->string('shipping_mode', 30)->nullable()->after('payment_method');
            $table->date('delivery_date_from')->nullable()->after('delivery_time_slot');
            $table->date('delivery_date_to')->nullable()->after('delivery_date_from');
        });
    }

    public function down(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_mode', 'delivery_date_from', 'delivery_date_to']);
        });
    }
};
