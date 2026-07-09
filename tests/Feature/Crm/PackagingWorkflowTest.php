<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackagingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createPackagingUser(): array
    {
        $role = Role::create(['name' => 'packaging', 'display_name' => 'Đóng gói vận chuyển', 'guard_name' => 'web']);
        $user = User::create([
            'name' => 'Packaging User',
            'email' => 'packaging@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'PKG100',
        ]);
        $user->crmRoles()->attach($role->id);

        return compact('role', 'user');
    }

    private function createWaitingOrder(): Order
    {
        $affiliate = Affiliate::create([
            'code' => 'CTV-PKG',
            'full_name' => 'CTV Packaging',
            'phone' => '0900000001',
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer',
            'phone' => '0900000002',
        ]);

        $product = Product::create([
            'sku' => 'BIKE-PKG',
            'name' => 'City Bike',
            'base_cost_jpy' => 40000,
            'recommended_price_jpy' => 55000,
        ]);

        return Order::create([
            'order_code' => 'HA-90001',
            'affiliate_id' => $affiliate->id,
            'customer_id' => $customer->id,
            'order_status' => 'waiting_delivery',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 55000,
            'total_cost_jpy' => 40000,
            'total_profit_jpy' => 15000,
            'created_by' => 1,
        ]);
    }

    private function attachDebt(Order $order): void
    {
        Debt::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'total_payable_jpy' => $order->total_amount_jpy,
            'total_paid_jpy' => 0,
            'outstanding_jpy' => $order->total_amount_jpy,
            'debt_status' => 'unpaid',
        ]);
    }

    public function test_packaging_user_can_progress_order_through_fulfillment(): void
    {
        ['user' => $user] = $this->createPackagingUser();
        $order = $this->createWaitingOrder();

        $this->actingAs($user)->getJson("/crm-api/orders/{$order->id}/transitions")
            ->assertOk()
            ->assertJsonPath('allowed_transitions', ['packaged', 'cancelled']);

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'packaged',
            'status_note' => 'Chuyển bộ phận: Đã đóng gói',
        ])->assertOk()->assertJsonPath('order_status', 'packaged');

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'delivering',
        ])->assertOk()->assertJsonPath('order_status', 'delivering');

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'delivered',
        ])->assertOk()->assertJsonPath('order_status', 'delivered');
    }

    public function test_packaging_user_only_sees_fulfillment_orders_in_list(): void
    {
        ['user' => $user] = $this->createPackagingUser();
        $waiting = $this->createWaitingOrder();
        Order::create([
            'order_code' => 'HA-90002',
            'affiliate_id' => $waiting->affiliate_id,
            'customer_id' => $waiting->customer_id,
            'order_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 1000,
            'total_cost_jpy' => 500,
            'total_profit_jpy' => 500,
            'created_by' => 1,
        ]);

        $response = $this->actingAs($user)->getJson('/crm-api/orders')->assertOk();
        $statuses = collect($response->json('data'))->pluck('order_status')->all();

        $this->assertSame(['waiting_delivery'], $statuses);
    }

    public function test_packaging_user_cannot_transition_confirmed_order(): void
    {
        ['user' => $user] = $this->createPackagingUser();
        $waiting = $this->createWaitingOrder();
        $order = Order::create([
            'order_code' => 'HA-90003',
            'affiliate_id' => $waiting->affiliate_id,
            'customer_id' => $waiting->customer_id,
            'order_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 1000,
            'total_cost_jpy' => 500,
            'total_profit_jpy' => 500,
            'created_by' => 1,
        ]);

        $this->actingAs($user)->getJson("/crm-api/orders/{$order->id}")->assertForbidden();
        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'confirmed',
        ])->assertForbidden();
    }

    public function test_packaging_user_can_submit_payment_pending_accounting_approval(): void
    {
        ['user' => $user] = $this->createPackagingUser();
        $order = $this->createWaitingOrder();
        $this->attachDebt($order);

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'packaged',
        ])->assertOk();

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'delivering',
        ])->assertOk();

        $this->actingAs($user)->putJson("/crm-api/orders/{$order->id}", [
            'order_status' => 'delivered',
        ])->assertOk();

        $response = $this->actingAs($user)->postJson("/crm-api/orders/{$order->id}/payments", [
            'amount_jpy' => 52000,
            'payment_type' => 'full',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'notes' => 'Shipper ghi nhận COD',
        ])->assertCreated();

        $this->assertDatabaseHas('crm_payments', [
            'id' => $response->json('id'),
            'order_id' => $order->id,
            'amount_jpy' => 52000,
            'approval_status' => 'pending',
            'recorded_by' => $user->id,
        ]);

        $this->assertDatabaseHas('crm_debts', [
            'order_id' => $order->id,
            'total_paid_jpy' => 0,
            'outstanding_jpy' => 55000,
        ]);
    }
}
