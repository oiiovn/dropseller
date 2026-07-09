<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createCollaborator(): array
    {
        $role = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-100', 'full_name' => 'CTV Demo', 'phone' => '0901000001']);
        $otherAffiliate = Affiliate::create(['code' => 'CTV-200', 'full_name' => 'CTV Other', 'phone' => '0901000002']);

        $user = User::create([
            'name' => 'Collaborator Demo',
            'email' => 'collab@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'COL100',
            'affiliate_id' => $affiliate->id,
        ]);
        $user->crmRoles()->attach($role->id);

        return compact('role', 'affiliate', 'otherAffiliate', 'user');
    }

    public function test_collaborator_customer_and_order_auto_assign_affiliate(): void
    {
        ['affiliate' => $affiliate, 'otherAffiliate' => $otherAffiliate, 'user' => $user] = $this->createCollaborator();

        $product = Product::create([
            'sku' => 'BIKE-100',
            'name' => 'City Bike',
            'base_cost_jpy' => 40000,
            'recommended_price_jpy' => 55000,
        ]);

        $customerResponse = $this->actingAs($user)->postJson('/crm-api/customers', [
            'full_name' => 'Tanaka',
            'phone' => '0801234567',
            'consultation_status' => 'consulting',
        ]);

        $customerResponse->assertCreated();
        $this->assertDatabaseHas('customers', [
            'id' => $customerResponse->json('id'),
            'affiliate_id' => $affiliate->id,
        ]);

        $orderResponse = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customerResponse->json('id'),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 55000,
                'unit_cost_jpy' => 40000,
            ]],
        ]);

        $orderResponse->assertCreated();
        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderResponse->json('id'),
            'affiliate_id' => $affiliate->id,
        ]);

        $foreignCustomer = Customer::create([
            'affiliate_id' => $otherAffiliate->id,
            'full_name' => 'Foreign Customer',
            'phone' => '0809999999',
        ]);

        $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $foreignCustomer->id,
            'affiliate_id' => $otherAffiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 55000,
                'unit_cost_jpy' => 40000,
            ]],
        ])->assertStatus(422);
    }

    public function test_bicycle_order_total_equals_bike_price_plus_shipping(): void
    {
        ['user' => $user] = $this->createCollaborator();

        $customer = Customer::create([
            'affiliate_id' => $user->affiliate_id,
            'full_name' => 'Bike Buyer',
            'phone' => '0801111222',
        ]);

        $response = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'product_name' => 'Yamaha 2024',
            'bike_price_jpy' => 56000,
            'shipping_fee_jpy' => 1000,
        ]);

        $response->assertCreated()
            ->assertJsonPath('subtotal_jpy', '56000.00')
            ->assertJsonPath('shipping_fee_jpy', '1000.00')
            ->assertJsonPath('total_amount_jpy', '57000.00');
    }

    public function test_order_show_syncs_incorrect_total_from_items_and_shipping(): void
    {
        ['user' => $user, 'affiliate' => $affiliate] = $this->createCollaborator();

        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Sync Total Buyer',
            'phone' => '0802222333',
        ]);

        $createResponse = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'product_name' => 'Xe đạp cải tiến',
            'bike_price_jpy' => 56000,
            'shipping_fee_jpy' => 1000,
        ]);

        $orderId = $createResponse->json('id');

        \DB::table('crm_orders')->where('id', $orderId)->update([
            'total_amount_jpy' => 1000,
            'discount_jpy' => 56000,
        ]);
        \DB::table('crm_debts')->where('order_id', $orderId)->update([
            'total_payable_jpy' => 1000,
            'outstanding_jpy' => 1000,
        ]);

        $this->actingAs($user)->getJson("/crm-api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('total_amount_jpy', '57000.00')
            ->assertJsonPath('debt.total_payable_jpy', '57000.00')
            ->assertJsonPath('debt.outstanding_jpy', '57000.00');
    }

    public function test_collaborator_order_status_workflow_and_commission_view_only(): void
    {
        ['affiliate' => $affiliate, 'user' => $user] = $this->createCollaborator();
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STF100',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer',
            'phone' => '0811111111',
        ]);

        $product = Product::create([
            'sku' => 'BIKE-200',
            'name' => 'Road Bike',
            'base_cost_jpy' => 50000,
            'recommended_price_jpy' => 65000,
        ]);

        $orderId = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'order_status' => 'new',
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 65000,
                'unit_cost_jpy' => 50000,
            ]],
        ])->json('id');

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'consulting',
        ])->assertOk();

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'completed',
        ])->assertStatus(422);

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'confirmed',
        ])->assertOk();

        $packagingRole = Role::create(['name' => 'packaging', 'display_name' => 'Đóng gói vận chuyển', 'guard_name' => 'web']);
        $packager = User::create([
            'name' => 'Packaging User',
            'email' => 'packaging@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'PKG100',
        ]);
        $packager->crmRoles()->attach($packagingRole->id);

        $this->actingAs($user)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'waiting_delivery',
            'status_note' => 'Chuyển bộ phận: Đóng gói',
        ])->assertOk()->assertJsonPath('order_status', 'waiting_delivery');

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'packaged',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivering',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivered',
        ])->assertOk();

        foreach (['completed'] as $status) {
            $this->actingAs($staff)->putJson("/crm-api/orders/{$orderId}", [
                'order_status' => $status,
            ])->assertOk();
        }

        $this->assertDatabaseHas('crm_commissions', [
            'order_id' => $orderId,
            'affiliate_id' => $affiliate->id,
        ]);

        $commissionId = $this->actingAs($user)->getJson('/crm-api/commissions')->json('data.0.id');

        $this->actingAs($user)->getJson("/crm-api/commissions/{$commissionId}")->assertOk();
        $this->actingAs($user)->postJson('/crm-api/commissions', [
            'order_id' => $orderId,
        ])->assertForbidden();
    }

    public function test_collaborator_scoped_dashboard_and_blocked_payment_create(): void
    {
        ['affiliate' => $affiliate, 'otherAffiliate' => $otherAffiliate, 'user' => $user] = $this->createCollaborator();

        $this->actingAs($user)->getJson('/crm-api/me')->assertOk()->assertJsonPath('affiliate_id', $affiliate->id);
        $this->actingAs($user)->getJson('/crm-api/me/affiliate')->assertOk()->assertJsonPath('code', 'CTV-100');
        $this->actingAs($user)->getJson('/crm-api/dashboard')->assertOk()->assertJsonPath('scope', 'collaborator');
        $this->actingAs($user)->getJson('/crm-api/affiliates')->assertForbidden();
        $this->actingAs($user)->postJson('/crm-api/payments', [])->assertForbidden();
    }
}
