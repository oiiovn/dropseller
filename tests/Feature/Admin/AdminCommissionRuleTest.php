<?php

namespace Tests\Feature\Admin;

use App\Models\Crm\Affiliate;
use App\Models\Crm\CommissionRule;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommissionRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_commission_rules_with_affiliates(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::create([
            'name' => 'Admin Rules',
            'email' => 'adminrules@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMRUL',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $affiliate = Affiliate::create([
            'code' => 'CTV-RUL',
            'full_name' => 'CTV Rule',
            'phone' => '0908888777',
            'status' => 'active',
        ]);

        $create = $this->actingAs($admin)->postJson('/admin-api/commission-rules', [
            'name' => 'Rule test',
            'rate_percent' => 6.5,
            'is_active' => true,
            'affiliate_ids' => [$affiliate->id],
        ])->assertCreated()
            ->assertJsonPath('name', 'Rule test')
            ->assertJsonPath('affiliates_count', 1);

        $ruleId = $create->json('id');

        $this->assertDatabaseHas('crm_commission_rule_affiliate', [
            'commission_rule_id' => $ruleId,
            'affiliate_id' => $affiliate->id,
        ]);

        $this->actingAs($admin)->putJson("/admin-api/commission-rules/{$ruleId}", [
            'name' => 'Rule updated',
            'rate_percent' => 7,
            'is_active' => false,
            'affiliate_ids' => [],
        ])->assertOk()->assertJsonPath('name', 'Rule updated');

        $this->actingAs($admin)->deleteJson("/admin-api/commission-rules/{$ruleId}")
            ->assertOk();

        $this->assertDatabaseMissing('crm_commission_rules', ['id' => $ruleId]);
    }
}
