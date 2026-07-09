<?php

namespace Tests\Feature\Admin;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_returns_overview_metrics(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-dashboard@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM-DASH',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $affiliate = Affiliate::create([
            'code' => 'CTV-DASH',
            'full_name' => 'CTV Dashboard',
            'phone' => '0901111222',
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Dash',
            'phone' => '0903333444',
        ]);

        $pendingOrder = Order::create([
            'order_code' => 'HA-10001',
            'affiliate_id' => $affiliate->id,
            'customer_id' => $customer->id,
            'order_status' => 'waiting_delivery',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 52000,
            'total_cost_jpy' => 30000,
            'total_profit_jpy' => 22000,
            'created_by' => $admin->id,
        ]);

        Order::create([
            'order_code' => 'HA-10002',
            'affiliate_id' => $affiliate->id,
            'customer_id' => $customer->id,
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'total_amount_jpy' => 48000,
            'total_cost_jpy' => 28000,
            'total_profit_jpy' => 20000,
            'created_by' => $admin->id,
        ]);

        Debt::create([
            'order_id' => $pendingOrder->id,
            'customer_id' => $customer->id,
            'total_payable_jpy' => 52000,
            'total_paid_jpy' => 0,
            'outstanding_jpy' => 52000,
            'debt_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->getJson('/admin-api/dashboard')->assertOk();

        $response
            ->assertJsonPath('total_orders', 2)
            ->assertJsonPath('completed_orders', 1)
            ->assertJsonPath('pending_orders_count', 1)
            ->assertJsonPath('total_revenue_jpy', 100000)
            ->assertJsonPath('outstanding_debt_jpy', 52000)
            ->assertJsonPath('effective_affiliates_count', 1)
            ->assertJsonStructure([
                'top_affiliates',
                'recent_pending_orders',
                'revenue_trend',
                'pending_orders_by_status',
                'latest_alerts',
                'commission_overview' => [
                    'wallet_unpaid_balance_jpy',
                    'wallet_paid_balance_jpy',
                    'period_commission_jpy',
                    'pending_settlements_count',
                    'top_affiliates',
                    'recent_commissions',
                    'trend',
                ],
            ]);

        $this->assertSame('HA-10001', $response->json('recent_pending_orders.0.order_code'));
        $this->assertSame('CTV-DASH', $response->json('top_affiliates.0.code'));
    }
}
