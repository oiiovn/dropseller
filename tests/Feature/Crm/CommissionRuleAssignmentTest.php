<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\CommissionRule;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use App\Services\Crm\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionRuleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_commission_uses_assigned_rule_not_category_match(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-RULE', 'full_name' => 'CTV Rule', 'phone' => '0901230999']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Rule',
            'phone' => '0812345999',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-RULE',
            'name' => 'City Bike',
            'category' => 'city',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 50000,
        ]);

        $assignedRule = CommissionRule::create([
            'name' => 'Rule VIP 8%',
            'rate_percent' => 8,
            'priority' => 1,
            'is_active' => true,
        ]);
        $assignedRule->affiliates()->attach($affiliate->id);

        CommissionRule::create([
            'name' => 'Rule city 12%',
            'product_category' => 'city',
            'rate_percent' => 12,
            'priority' => 1,
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Admin Rule',
            'email' => 'adminrule@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'RULADM',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $orderId = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 50000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->assertCreated()->json('id');

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'completed',
        ])->assertOk();

        $this->assertDatabaseHas('crm_commissions', [
            'order_id' => $orderId,
            'commission_rule_id' => $assignedRule->id,
            'commission_rate' => 8.00,
            'commission_amount_jpy' => 4000.00,
        ]);
    }

    public function test_syncing_affiliate_to_rule_removes_previous_assignment(): void
    {
        $affiliate = Affiliate::create(['code' => 'CTV-MOVE', 'full_name' => 'CTV Move', 'phone' => '0901230888']);

        $ruleA = CommissionRule::create([
            'name' => 'Rule A',
            'rate_percent' => 5,
            'is_active' => true,
        ]);
        $ruleB = CommissionRule::create([
            'name' => 'Rule B',
            'rate_percent' => 7,
            'is_active' => true,
        ]);

        $service = app(CommissionService::class);
        $service->syncRuleAffiliates($ruleA, [$affiliate->id]);
        $service->syncRuleAffiliates($ruleB, [$affiliate->id]);

        $this->assertDatabaseMissing('crm_commission_rule_affiliate', [
            'commission_rule_id' => $ruleA->id,
            'affiliate_id' => $affiliate->id,
        ]);

        $this->assertDatabaseHas('crm_commission_rule_affiliate', [
            'commission_rule_id' => $ruleB->id,
            'affiliate_id' => $affiliate->id,
        ]);
    }
}
