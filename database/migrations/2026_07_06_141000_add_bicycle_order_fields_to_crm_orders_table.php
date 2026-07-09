<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->string('facebook_url', 500)->nullable()->after('notes');
            $table->string('battery_capacity', 100)->nullable()->after('facebook_url');
            $table->string('payment_method')->nullable()->after('battery_capacity');
            $table->date('delivery_date')->nullable()->after('payment_method');
            $table->string('delivery_time_slot', 120)->nullable()->after('delivery_date');
        });
    }

    public function down(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'battery_capacity',
                'payment_method',
                'delivery_date',
                'delivery_time_slot',
            ]);
        });
    }
};
