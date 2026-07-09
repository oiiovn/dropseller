<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\CommissionRule;
use App\Models\Crm\Customer;
use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_commission_rule_applies_from_affiliate_assignment(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-P2A', 'full_name' => 'CTV P2A', 'phone' => '0909990001']);
        $customer = Customer::create(['affiliate_id' => $affiliate->id, 'full_name' => 'Customer P2A', 'phone' => '0811000001']);
        $product = Product::create([
            'sku' => 'BIKE-S-01',
            'name' => 'Sport Bike',
            'category' => 'sports',
            'base_cost_jpy' => 40000,
            'recommended_price_jpy' => 60000,
        ]);

        $rule = CommissionRule::create([
            'name' => 'Sports Rule',
            'product_category' => 'sports',
            'rate_percent' => 9,
            'priority' => 1,
            'is_active' => true,
        ]);
        $rule->affiliates()->attach($affiliate->id);

        CommissionRule::create([
            'name' => 'Default Rule',
            'rate_percent' => 5,
            'priority' => 100,
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Admin P2A',
            'email' => 'adminp2a@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'P2A001',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $orderRes = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 60000,
                'unit_cost_jpy' => 40000,
            ]],
        ]);
        $orderId = $orderRes->json('id');

        foreach (['waiting_delivery', 'delivering', 'delivered', 'completed'] as $status) {
            $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
                'order_status' => $status,
            ])->assertOk();
        }

        $this->assertDatabaseHas('crm_commissions', [
            'order_id' => $orderId,
            'commission_rule_id' => $rule->id,
            'commission_rate' => 9.00,
        ]);
    }

    public function test_overdue_debt_alert_endpoint_returns_alerts(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-P2B', 'full_name' => 'CTV P2B', 'phone' => '0909990002']);
        $customer = Customer::create(['affiliate_id' => $affiliate->id, 'full_name' => 'Customer P2B', 'phone' => '0811000002']);
        $order = Order::create([
            'order_code' => 'HIMA-P2B',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'confirmed',
            'total_amount_jpy' => 50000,
            'total_cost_jpy' => 30000,
            'total_profit_jpy' => 20000,
            'debt_status' => 'unpaid',
        ]);

        Debt::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'total_payable_jpy' => 50000,
            'total_paid_jpy' => 0,
            'outstanding_jpy' => 50000,
            'due_date' => now()->subDays(2)->toDateString(),
            'debt_status' => 'unpaid',
        ]);

        $user = User::create([
            'name' => 'Admin P2B',
            'email' => 'adminp2b@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'P2B001',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $response = $this->actingAs($user)->getJson('/crm-api/alerts/overdue-debts');
        $response->assertOk();
        $response->assertJsonPath('summary.overdue_count', 1);
        $this->assertDatabaseHas('crm_alerts', ['type' => 'overdue_debt']);
    }

    public function test_advanced_report_endpoint_returns_series(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-P2C', 'full_name' => 'CTV P2C', 'phone' => '0909990003']);
        $customer = Customer::create(['affiliate_id' => $affiliate->id, 'full_name' => 'Customer P2C', 'phone' => '0811000003']);

        Order::create([
            'order_code' => 'HIMA-P2C-1',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'completed',
            'total_amount_jpy' => 88000,
            'total_cost_jpy' => 60000,
            'total_profit_jpy' => 28000,
            'debt_status' => 'cleared',
        ]);

        $user = User::create([
            'name' => 'Admin P2C',
            'email' => 'adminp2c@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'P2C001',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $response = $this->actingAs($user)->getJson('/crm-api/reports/advanced?granularity=month&periods=3');
        $response->assertOk();
        $response->assertJsonPath('granularity', 'month');
        $this->assertNotEmpty($response->json('series'));
    }
}
