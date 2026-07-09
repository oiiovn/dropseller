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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('locale', 10)->default('vi')->after('phone');
            $table->string('currency', 10)->default('JPY')->after('locale');
            $table->foreignId('affiliate_id')->nullable()->after('currency')->constrained('affiliates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_id');
            $table->dropColumn(['phone', 'locale', 'currency']);
        });
    }
};
