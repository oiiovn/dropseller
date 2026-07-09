<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\Crm\UserNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmNotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_submit_notifies_accounting_and_approval_notifies_submitter(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $accountingRole = Role::firstOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Accounting', 'guard_name' => 'web']
        );

        $affiliate = Affiliate::create(['code' => 'CTV-NOTI', 'full_name' => 'CTV Noti', 'phone' => '0901230099']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Noti',
            'phone' => '0812345699',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-NOTI',
            'name' => 'Noti Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $staff = User::create([
            'name' => 'Staff Noti',
            'email' => 'staffnoti@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STFNOTI',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $accounting = User::create([
            'name' => 'Accounting Noti',
            'email' => 'accountingnoti@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ACCNOTI',
        ]);
        $accounting->crmRoles()->attach($accountingRole->id);

        $admin = User::create([
            'name' => 'Admin Noti',
            'email' => 'adminnoti@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMNOTI',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $orderResponse = $this->actingAs($staff)->postJson('/crm-api/orders', [
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
        ])->assertCreated();

        $orderId = $orderResponse->json('id');

        $this->assertDatabaseHas('crm_user_notifications', [
            'user_id' => $admin->id,
            'type' => 'order_created',
        ]);

        $paymentResponse = $this->actingAs($staff)->postJson('/crm-api/payments', [
            'order_id' => $orderId,
            'customer_id' => $customer->id,
            'amount_jpy' => 20000,
            'payment_type' => 'deposit',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertCreated();

        $paymentId = $paymentResponse->json('id');

        $this->assertTrue(
            UserNotification::query()
                ->where('type', 'payment_pending')
                ->where('user_id', $accounting->id)
                ->exists()
        );

        $this->actingAs($accounting)->putJson("/crm-api/payments/{$paymentId}/approve")->assertOk();

        $this->assertTrue(
            UserNotification::query()
                ->where('type', 'payment_approved')
                ->where('user_id', $staff->id)
                ->exists()
        );
    }

    public function test_notification_api_returns_user_notifications(): void
    {
        $role = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $user = User::create([
            'name' => 'API User',
            'email' => 'apiuser@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'APINOTI',
        ]);
        $user->crmRoles()->attach($role->id);

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'order_created',
            'category' => 'info',
            'title' => 'Test notification',
            'message' => 'Test message',
            'action_url' => '/crm/orders/1',
        ]);

        $this->actingAs($user)
            ->getJson('/crm-api/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonCount(1, 'notifications');
    }
}
