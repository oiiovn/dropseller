<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionSettlement;
use App\Models\Crm\CommissionWallet;
use App\Models\Crm\Role;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionWalletFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_order_credits_commission_wallet(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-001', 'full_name' => 'Tuyet Nhi', 'phone' => '0901230001']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer One',
            'phone' => '0812345678',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-001',
            'name' => 'Road Bike',
            'base_cost_jpy' => 50000,
            'recommended_price_jpy' => 65000,
        ]);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM001',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $createResponse = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price_jpy' => 65000,
                    'unit_cost_jpy' => 50000,
                ],
            ],
        ]);

        $orderId = $createResponse->json('id');
        $statusPath = ['consulting', 'confirmed', 'deposit_pending', 'deposit_paid', 'waiting_delivery', 'delivering', 'delivered', 'completed'];
        $currentStatus = $createResponse->json('order_status');
        $startIndex = array_search($currentStatus, $statusPath, true);
        if ($currentStatus === 'new' || $startIndex === false) {
            $startIndex = -1;
        }

        for ($index = $startIndex + 1; $index < count($statusPath); $index++) {
            $status = $statusPath[$index];
            $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
                'order_status' => $status,
            ])->assertOk();
        }

        $this->assertDatabaseHas('crm_commissions', [
            'order_id' => $orderId,
            'affiliate_id' => $affiliate->id,
            'wallet_status' => 'credited',
        ]);

        $wallet = CommissionWallet::where('affiliate_id', $affiliate->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(3250, (float) $wallet->unpaid_balance_jpy);

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'refunded',
        ])->assertOk();

        $this->assertDatabaseHas('crm_commissions', [
            'order_id' => $orderId,
            'wallet_status' => 'cancelled',
        ]);

        $wallet->refresh();
        $this->assertEquals(0, (float) $wallet->unpaid_balance_jpy);
    }

    public function test_accounting_can_settle_monthly_commission_wallet(): void
    {
        $accountingRole = Role::firstOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Accounting', 'guard_name' => 'web']
        );
        $affiliate = Affiliate::create(['code' => 'CTV-002', 'full_name' => 'CTV Two', 'phone' => '0901230002']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Two',
            'phone' => '0812345679',
        ]);

        $accountant = User::create([
            'name' => 'Accountant',
            'email' => 'accounting@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ACC001',
        ]);
        $accountant->crmRoles()->attach($accountingRole->id);

        $order = \App\Models\Crm\Order::create([
            'order_code' => 'HIMA-001',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'total_amount_jpy' => 20000,
            'subtotal_jpy' => 20000,
        ]);

        Commission::create([
            'order_id' => $order->id,
            'affiliate_id' => $affiliate->id,
            'commission_rate' => 5,
            'commission_amount_jpy' => 1000,
            'applied_rule_name' => 'Mặc định',
            'approval_status' => 'approved',
            'payment_status' => 'unpaid',
            'wallet_status' => 'credited',
            'credited_at' => now(),
            'completed_at' => now(),
        ]);

        CommissionWallet::create([
            'affiliate_id' => $affiliate->id,
            'unpaid_balance_jpy' => 1000,
            'paid_balance_jpy' => 0,
            'total_commission_jpy' => 1000,
        ]);

        $periodMonth = now()->format('Y-m');

        $createSettlement = $this->actingAs($accountant)->postJson("/crm-api/commission-wallets/{$affiliate->id}/settlements", [
            'period_month' => $periodMonth,
        ]);

        $createSettlement->assertCreated();
        $settlementId = $createSettlement->json('id');

        $this->actingAs($accountant)->putJson("/crm-api/commission-settlements/{$settlementId}/settle")->assertOk();

        $this->assertDatabaseHas('crm_commission_settlements', [
            'id' => $settlementId,
            'status' => 'paid',
        ]);

        $wallet = CommissionWallet::where('affiliate_id', $affiliate->id)->first();
        $this->assertEquals(0, (float) $wallet->unpaid_balance_jpy);
        $this->assertEquals(1000, (float) $wallet->paid_balance_jpy);
    }

    public function test_commission_wallet_list_supports_accounting_filters(): void
    {
        $accountingRole = Role::firstOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Accounting', 'guard_name' => 'web']
        );

        $affiliateWithUnpaid = Affiliate::create([
            'code' => 'CTV-FILTER-1',
            'full_name' => 'Nguyen Van A',
            'phone' => '0901111001',
            'status' => 'active',
        ]);
        $affiliateSettled = Affiliate::create([
            'code' => 'CTV-FILTER-2',
            'full_name' => 'Tran Thi B',
            'phone' => '0901111002',
            'status' => 'inactive',
        ]);

        CommissionWallet::create([
            'affiliate_id' => $affiliateWithUnpaid->id,
            'unpaid_balance_jpy' => 5000,
            'paid_balance_jpy' => 0,
            'total_commission_jpy' => 5000,
        ]);
        CommissionWallet::create([
            'affiliate_id' => $affiliateSettled->id,
            'unpaid_balance_jpy' => 0,
            'paid_balance_jpy' => 3000,
            'total_commission_jpy' => 3000,
        ]);

        CommissionSettlement::create([
            'affiliate_id' => $affiliateWithUnpaid->id,
            'period_month' => now()->format('Y-m'),
            'total_amount_jpy' => 5000,
            'status' => 'pending',
        ]);

        $accountant = User::create([
            'name' => 'Accountant Filter',
            'email' => 'accounting-filter@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ACCFLT',
        ]);
        $accountant->crmRoles()->attach($accountingRole->id);

        $this->actingAs($accountant)
            ->getJson('/crm-api/commission-wallets?search=Nguyen')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.code', 'CTV-FILTER-1');

        $this->actingAs($accountant)
            ->getJson('/crm-api/commission-wallets?unpaid_only=1')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.affiliate_id', $affiliateWithUnpaid->id);

        $this->actingAs($accountant)
            ->getJson('/crm-api/commission-wallets?has_pending_settlement=1')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.pending_settlements_count', 1);

        $this->actingAs($accountant)
            ->getJson('/crm-api/commission-wallets?status=inactive')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.code', 'CTV-FILTER-2');
    }
}
