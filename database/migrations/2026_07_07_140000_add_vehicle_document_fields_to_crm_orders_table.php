<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->string('vehicle_chassis_number', 120)->nullable()->after('delivery_date_to');
            $table->string('vehicle_owner_name_vi', 255)->nullable()->after('vehicle_chassis_number');
            $table->string('vehicle_owner_name_ja', 255)->nullable()->after('vehicle_owner_name_vi');
            $table->string('vehicle_owner_postal_code', 20)->nullable()->after('vehicle_owner_name_ja');
            $table->string('vehicle_owner_phone', 30)->nullable()->after('vehicle_owner_postal_code');
            $table->text('vehicle_owner_address')->nullable()->after('vehicle_owner_phone');
        });
    }

    public function down(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_chassis_number',
                'vehicle_owner_name_vi',
                'vehicle_owner_name_ja',
                'vehicle_owner_postal_code',
                'vehicle_owner_phone',
                'vehicle_owner_address',
            ]);
        });
    }
};
