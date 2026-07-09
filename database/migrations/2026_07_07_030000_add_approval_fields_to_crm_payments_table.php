<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->string('approval_status', 30)->default('pending')->after('payment_status')->index();
            $table->foreignId('approved_by')->nullable()->after('recorded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_note')->nullable()->after('approved_at');
        });

        DB::table('crm_payments')->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['approval_status', 'approved_at', 'rejection_note']);
        });
    }
};
