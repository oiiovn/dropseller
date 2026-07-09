<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_commission_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->unique()->constrained('affiliates')->cascadeOnDelete();
            $table->decimal('unpaid_balance_jpy', 14, 2)->default(0);
            $table->decimal('paid_balance_jpy', 14, 2)->default(0);
            $table->decimal('total_commission_jpy', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('crm_commission_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->string('period_month', 7)->index();
            $table->decimal('total_amount_jpy', 14, 2)->default(0);
            $table->string('status', 30)->default('pending')->index();
            $table->date('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['affiliate_id', 'period_month']);
        });

        Schema::table('crm_commissions', function (Blueprint $table) {
            $table->string('wallet_status', 30)->default('pending_record')->index()->after('payment_status');
            $table->timestamp('completed_at')->nullable()->after('wallet_status');
            $table->timestamp('credited_at')->nullable()->after('completed_at');
            $table->foreignId('settlement_id')->nullable()->after('credited_at')->constrained('crm_commission_settlements')->nullOnDelete();
            $table->foreignId('clawback_for_commission_id')->nullable()->after('settlement_id')->constrained('crm_commissions')->nullOnDelete();
        });

        $this->backfillExistingCommissions();
    }

    public function down(): void
    {
        Schema::table('crm_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clawback_for_commission_id');
            $table->dropConstrainedForeignId('settlement_id');
            $table->dropColumn(['wallet_status', 'completed_at', 'credited_at']);
        });

        Schema::dropIfExists('crm_commission_settlements');
        Schema::dropIfExists('crm_commission_wallets');
    }

    private function backfillExistingCommissions(): void
    {
        if (! Schema::hasTable('crm_commissions')) {
            return;
        }

        $commissions = DB::table('crm_commissions')->get();

        foreach ($commissions as $commission) {
            $walletStatus = match ($commission->payment_status) {
                'paid' => 'settled',
                'partial' => 'pending_settlement',
                default => 'credited',
            };

            DB::table('crm_commissions')
                ->where('id', $commission->id)
                ->update([
                    'wallet_status' => $walletStatus,
                    'credited_at' => $commission->created_at,
                    'completed_at' => $commission->created_at,
                ]);

            $wallet = DB::table('crm_commission_wallets')
                ->where('affiliate_id', $commission->affiliate_id)
                ->first();

            if (! $wallet) {
                DB::table('crm_commission_wallets')->insert([
                    'affiliate_id' => $commission->affiliate_id,
                    'unpaid_balance_jpy' => 0,
                    'paid_balance_jpy' => 0,
                    'total_commission_jpy' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $amount = (float) $commission->commission_amount_jpy;

            if ($walletStatus === 'settled') {
                DB::table('crm_commission_wallets')
                    ->where('affiliate_id', $commission->affiliate_id)
                    ->update([
                        'paid_balance_jpy' => DB::raw("paid_balance_jpy + {$amount}"),
                        'total_commission_jpy' => DB::raw("total_commission_jpy + {$amount}"),
                        'updated_at' => now(),
                    ]);
            } elseif ($walletStatus !== 'cancelled') {
                DB::table('crm_commission_wallets')
                    ->where('affiliate_id', $commission->affiliate_id)
                    ->update([
                        'unpaid_balance_jpy' => DB::raw("unpaid_balance_jpy + {$amount}"),
                        'total_commission_jpy' => DB::raw("total_commission_jpy + {$amount}"),
                        'updated_at' => now(),
                    ]);
            }
        }
    }
};
