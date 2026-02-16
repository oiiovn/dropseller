<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_wallet_balance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 50)->index();
            $table->string('bank', 50)->nullable();
            $table->decimal('balance_snapshot', 18, 2)->default(0);
            $table->date('as_of_date')->comment('Số dư tại ngày này; giao dịch sau ngày này sẽ cộng/trừ');
            $table->timestamps();
        });

        $now = now()->toDateTimeString();
        \DB::table('bank_wallet_balance_snapshots')->insert([
            'account_number' => '46241987',
            'bank' => 'ACB',
            'balance_snapshot' => 82717978,
            'as_of_date' => now()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_wallet_balance_snapshots');
    }
};
