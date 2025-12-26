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
        Schema::table('pick_orders', function (Blueprint $table) {
            $table->integer('original_quantity')->default(0)->after('quantity_sold')->comment('Số lượng ban đầu khi upload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pick_orders', function (Blueprint $table) {
            $table->dropColumn('original_quantity');
        });
    }
};
