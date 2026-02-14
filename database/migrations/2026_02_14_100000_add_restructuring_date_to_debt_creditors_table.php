<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debt_creditors', function (Blueprint $table) {
            $table->date('restructuring_date')->nullable()->after('notes')->comment('Ngày tái cấu trúc');
        });
    }

    public function down(): void
    {
        Schema::table('debt_creditors', function (Blueprint $table) {
            $table->dropColumn('restructuring_date');
        });
    }
};
