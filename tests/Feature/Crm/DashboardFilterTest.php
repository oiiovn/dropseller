<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_filters_orders_by_month(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-DASH', 'full_name' => 'CTV Dash', 'phone' => '0901111222']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Dash',
            'phone' => '0811111222',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-DASH',
            'name' => 'Dash Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $admin = User::create([
            'name' => 'Admin Dash',
            'email' => 'admindash@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMDASH',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $this->actingAs($admin)->postJson('/crm-api/orders', [
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

        $month = now()->format('Y-m');

        $this->actingAs($admin)
            ->getJson('/crm-api/dashboard?month=' . $month)
            ->assertOk()
            ->assertJsonPath('total_orders', 1)
            ->assertJsonPath('filter.date_from', now()->startOfMonth()->toDateString())
            ->assertJsonPath('filter.date_to', now()->endOfMonth()->toDateString());

        $pastFrom = now()->subMonths(2)->startOfMonth()->toDateString();
        $pastTo = now()->subMonths(2)->endOfMonth()->toDateString();

        $this->actingAs($admin)
            ->getJson('/crm-api/dashboard?date_from=' . $pastFrom . '&date_to=' . $pastTo)
            ->assertOk()
            ->assertJsonPath('total_orders', 0);
    }
}
