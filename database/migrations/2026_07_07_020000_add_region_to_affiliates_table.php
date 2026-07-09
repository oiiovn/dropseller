<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->string('region', 20)->nullable()->after('area')->index();
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
};
