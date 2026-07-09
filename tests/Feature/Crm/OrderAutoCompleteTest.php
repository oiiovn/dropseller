<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAutoCompleteTest extends TestCase
{
    use RefreshDatabase;

    private function seedOrderContext(): array
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $packagingRole = Role::create(['name' => 'packaging', 'display_name' => 'Packaging', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-AUTO', 'full_name' => 'CTV Auto', 'phone' => '0901230101']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Auto',
            'phone' => '0812345101',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-AUTO',
            'name' => 'Auto Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $admin = User::create([
            'name' => 'Admin Auto',
            'email' => 'adminauto@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMAUTO',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $packager = User::create([
            'name' => 'Packager Auto',
            'email' => 'packagerauto@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'PKG001',
        ]);
        $packager->crmRoles()->attach($packagingRole->id);

        $orderId = $this->actingAs($admin)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 45000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->assertCreated()->json('id');

        return compact('admin', 'packager', 'customer', 'orderId');
    }

    public function test_delivered_order_auto_completes_after_full_payment_approval(): void
    {
        ['admin' => $admin, 'packager' => $packager, 'customer' => $customer, 'orderId' => $orderId] = $this->seedOrderContext();

        $this->actingAs($admin)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'waiting_delivery',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'packaged',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivering',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivered',
        ])->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'delivered',
            'payment_status' => 'unpaid',
        ]);

        $paymentId = $this->actingAs($admin)->postJson('/crm-api/payments', [
            'order_id' => $orderId,
            'customer_id' => $customer->id,
            'amount_jpy' => 45000,
            'payment_type' => 'full',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated()->json('id');

        $this->actingAs($admin)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    public function test_paid_order_auto_completes_when_marked_delivered(): void
    {
        ['admin' => $admin, 'packager' => $packager, 'customer' => $customer, 'orderId' => $orderId] = $this->seedOrderContext();

        $paymentId = $this->actingAs($admin)->postJson('/crm-api/payments', [
            'order_id' => $orderId,
            'customer_id' => $customer->id,
            'amount_jpy' => 45000,
            'payment_type' => 'full',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated()->json('id');

        $this->actingAs($admin)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'waiting_delivery',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'packaged',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivering',
        ])->assertOk();

        $this->actingAs($packager)->putJson("/crm-api/orders/{$orderId}", [
            'order_status' => 'delivered',
        ])->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }
}
