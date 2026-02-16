<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();
        DB::table('bank_wallet_balance_snapshots')->insert([
            'account_number' => '008338298888',
            'bank' => 'MBB',
            'balance_snapshot' => 0,
            'as_of_date' => now()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('bank_wallet_balance_snapshots')
            ->where('account_number', '008338298888')
            ->where('bank', 'MBB')
            ->delete();
    }
};
