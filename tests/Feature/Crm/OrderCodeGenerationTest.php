<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use App\Services\Crm\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_code_uses_ha_prefix_and_is_unique(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-HA', 'full_name' => 'CTV HA', 'phone' => '0901111333']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer HA',
            'phone' => '0811333444',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-HA',
            'name' => 'HA Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 50000,
        ]);

        $user = User::create([
            'name' => 'Admin HA',
            'email' => 'adminha@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMHA',
        ]);
        $user->crmRoles()->attach($adminRole->id);

        $payload = [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'bike_price_jpy' => 50000,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 50000,
                'unit_cost_jpy' => 30000,
            ]],
        ];

        $first = app(OrderService::class)->create($payload, $user->id);
        $second = app(OrderService::class)->create($payload, $user->id);

        $this->assertMatchesRegularExpression('/^HA-\d{5}$/', $first->order_code);
        $this->assertMatchesRegularExpression('/^HA-\d{5}$/', $second->order_code);
        $this->assertNotSame($first->order_code, $second->order_code);
        $this->assertNotNull($first->confirmed_at);
    }
}
