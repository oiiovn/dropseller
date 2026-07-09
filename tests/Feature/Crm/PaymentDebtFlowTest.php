<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDebtFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_requires_accounting_approval_before_updating_debt(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-PAY', 'full_name' => 'CTV Pay', 'phone' => '0901230002']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Pay',
            'phone' => '0812345679',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-002',
            'name' => 'City Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $user = User::create([
            'name' => 'Admin Pay',
            'email' => 'adminpay@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM002',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $orderResponse = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price_jpy' => 45000,
                    'unit_cost_jpy' => 30000,
                ],
            ],
        ]);
        $orderId = $orderResponse->json('id');

        $paymentResponse = $this->actingAs($user)->postJson('/crm-api/payments', [
            'order_id' => $orderId,
            'customer_id' => $customer->id,
            'amount_jpy' => 20000,
            'payment_type' => 'deposit',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated();

        $paymentId = $paymentResponse->json('id');

        $this->assertDatabaseHas('crm_payments', [
            'id' => $paymentId,
            'approval_status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertDatabaseHas('crm_debts', [
            'order_id' => $orderId,
            'total_paid_jpy' => 0.00,
            'outstanding_jpy' => 45000.00,
        ]);

        $this->actingAs($user)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_debts', [
            'order_id' => $orderId,
            'total_paid_jpy' => 20000.00,
            'outstanding_jpy' => 25000.00,
            'debt_status' => 'partial',
        ]);

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'deposit_paid',
        ]);
    }

    public function test_deposit_on_deposit_pending_order_advances_status_after_approval(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-PAY2', 'full_name' => 'CTV Pay 2', 'phone' => '0901230003']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Pay 2',
            'phone' => '0812345680',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-003',
            'name' => 'City Bike 2',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $user = User::create([
            'name' => 'Admin Pay 2',
            'email' => 'adminpay2@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM003',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $orderId = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'deposit_pending',
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 45000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->json('id');

        $paymentId = $this->actingAs($user)->postJson("/crm-api/orders/{$orderId}/payments", [
            'payment_type' => 'deposit',
            'amount_jpy' => 10000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated()->json('id');

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'deposit_pending',
        ]);

        $this->actingAs($user)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'order_status' => 'deposit_paid',
        ]);

        $this->actingAs($user)->getJson("/crm-api/orders/{$orderId}/transitions")
            ->assertOk()
            ->assertJsonFragment(['current_status' => 'deposit_paid'])
            ->assertJsonPath('allowed_transitions', fn ($transitions) => in_array('waiting_delivery', $transitions, true));
    }

    public function test_accounting_role_can_approve_payment(): void
    {
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $accountingRole = Role::firstOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Kế toán', 'guard_name' => 'web']
        );
        $affiliate = Affiliate::create(['code' => 'CTV-ACC', 'full_name' => 'CTV Acc', 'phone' => '0901230004']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Acc',
            'phone' => '0812345681',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-004',
            'name' => 'City Bike 4',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staffacc@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STF001',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $accountant = User::create([
            'name' => 'Accountant',
            'email' => 'accountant@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ACC001',
        ]);
        $accountant->crmRoles()->attach($accountingRole->id);

        $orderId = $this->actingAs($staff)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 45000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->json('id');

        $paymentId = $this->actingAs($staff)->postJson('/crm-api/payments', [
            'order_id' => $orderId,
            'customer_id' => $customer->id,
            'amount_jpy' => 15000,
            'payment_type' => 'partial',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ])->assertCreated()->json('id');

        $this->actingAs($accountant)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_payments', [
            'id' => $paymentId,
            'approval_status' => 'approved',
            'approved_by' => $accountant->id,
        ]);
    }

    public function test_accounting_cannot_submit_payment(): void
    {
        $accountingRole = Role::firstOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Kế toán', 'guard_name' => 'web']
        );
        $affiliate = Affiliate::create(['code' => 'CTV-ACC2', 'full_name' => 'CTV Acc 2', 'phone' => '0901230005']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Acc 2',
            'phone' => '0812345682',
        ]);

        $accountant = User::create([
            'name' => 'Accountant Only',
            'email' => 'accountantonly@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ACC002',
        ]);
        $accountant->crmRoles()->attach($accountingRole->id);

        $order = Order::create([
            'order_code' => 'HA-99999',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 45000,
            'subtotal_jpy' => 45000,
        ]);

        $this->actingAs($accountant)->postJson('/crm-api/payments', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'amount_jpy' => 10000,
            'payment_type' => 'deposit',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertForbidden();
    }

    public function test_reject_payment_requires_reason(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-REJ', 'full_name' => 'CTV Rej', 'phone' => '0901230006']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Rej',
            'phone' => '0812345683',
        ]);

        $user = User::create([
            'name' => 'Admin Rej',
            'email' => 'adminrej@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM004',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $order = Order::create([
            'order_code' => 'HA-88888',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 45000,
            'subtotal_jpy' => 45000,
        ]);

        $paymentId = \App\Models\Crm\Payment::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'amount_jpy' => 10000,
            'payment_type' => 'deposit',
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'payment_status' => 'pending',
            'approval_status' => 'pending',
            'recorded_by' => $user->id,
        ])->id;

        $this->actingAs($user)->putJson("/crm-api/payments/{$paymentId}/reject", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['rejection_note']);

        $this->actingAs($user)->putJson("/crm-api/payments/{$paymentId}/reject", [
            'rejection_note' => 'Sai số tiền chuyển khoản',
        ])->assertOk();

        $this->assertDatabaseHas('crm_payments', [
            'id' => $paymentId,
            'approval_status' => 'rejected',
            'rejection_note' => 'Sai số tiền chuyển khoản',
        ]);
    }

    public function test_payment_discount_applies_to_order_after_accounting_approval(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-DISC', 'full_name' => 'CTV Disc', 'phone' => '0901230099']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Disc',
            'phone' => '0812345699',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-DISC',
            'name' => 'City Bike Disc',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 52000,
        ]);

        $user = User::create([
            'name' => 'Admin Disc',
            'email' => 'admindisc@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM099',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $orderId = $this->actingAs($user)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 52000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->assertCreated()->json('id');

        $paymentId = $this->actingAs($user)->postJson("/crm-api/orders/{$orderId}/payments", [
            'payment_type' => 'full',
            'amount_jpy' => 50000,
            'discount_jpy' => 2000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated()->json('id');

        $this->assertDatabaseHas('crm_payments', [
            'id' => $paymentId,
            'discount_jpy' => 2000,
            'approval_status' => 'pending',
        ]);

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'discount_jpy' => 0,
            'total_amount_jpy' => 52000,
        ]);

        $this->actingAs($user)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'discount_jpy' => 2000,
            'total_amount_jpy' => 50000,
        ]);

        $this->assertDatabaseHas('crm_debts', [
            'order_id' => $orderId,
            'total_payable_jpy' => 50000,
            'total_paid_jpy' => 50000,
            'outstanding_jpy' => 0,
        ]);
    }
}
