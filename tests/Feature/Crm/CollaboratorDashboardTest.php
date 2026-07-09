<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_collaborator_dashboard_includes_charts_and_commission_overview(): void
    {
        $role = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-DASH', 'full_name' => 'CTV Dashboard', 'phone' => '0901000099']);

        $user = User::create([
            'name' => 'CTV Dashboard User',
            'email' => 'ctv-dashboard@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTVDASH',
            'affiliate_id' => $affiliate->id,
        ]);
        $user->crmRoles()->attach($role->id);

        $month = now()->format('Y-m');

        $this->actingAs($user)
            ->getJson('/crm-api/dashboard?month=' . $month)
            ->assertOk()
            ->assertJsonPath('scope', 'collaborator')
            ->assertJsonStructure([
                'revenue_trend' => [
                    ['period_key', 'label', 'orders', 'revenue_jpy', 'completed_orders'],
                ],
                'commission_trend' => [
                    ['period_key', 'label', 'commission_count', 'commission_jpy', 'settled_jpy'],
                ],
                'commission_overview' => [
                    'wallet_unpaid_balance_jpy',
                    'wallet_paid_balance_jpy',
                    'wallet_total_commission_jpy',
                    'period_commission_jpy',
                    'period_commission_count',
                    'pending_settlements_count',
                    'by_wallet_status',
                    'recent_commissions',
                ],
                'recent_orders',
                'pending_orders_count',
                'pending_orders_by_status',
                'affiliate' => ['id', 'code', 'full_name'],
            ]);
    }
}
