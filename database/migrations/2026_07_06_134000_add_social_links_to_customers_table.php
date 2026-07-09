<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('facebook_url', 500)->nullable()->after('address');
            $table->string('google_maps_url', 500)->nullable()->after('facebook_url');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['facebook_url', 'google_maps_url']);
        });
    }
};
