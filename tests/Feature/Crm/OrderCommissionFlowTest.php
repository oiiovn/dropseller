<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCommissionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_order_generates_commission(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-001', 'full_name' => 'CTV One', 'phone' => '0901230001']);
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

        $createResponse->assertCreated();
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
    }
}
