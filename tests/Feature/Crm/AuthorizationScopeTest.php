<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\Crm\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_collaborator_only_sees_own_customers(): void
    {
        $role = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);

        $affiliateA = Affiliate::create([
            'code' => 'CTV-A',
            'full_name' => 'CTV A',
            'phone' => '0900000001',
        ]);

        $affiliateB = Affiliate::create([
            'code' => 'CTV-B',
            'full_name' => 'CTV B',
            'phone' => '0900000002',
        ]);

        Customer::create([
            'affiliate_id' => $affiliateA->id,
            'full_name' => 'Customer A',
            'phone' => '0811111111',
        ]);

        Customer::create([
            'affiliate_id' => $affiliateB->id,
            'full_name' => 'Customer B',
            'phone' => '0822222222',
        ]);

        $user = User::create([
            'name' => 'Collaborator',
            'email' => 'ctv@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTV001',
            'affiliate_id' => $affiliateA->id,
        ]);
        $user->crmRoles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/crm-api/customers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.full_name', 'Customer A');
    }

    public function test_customer_list_supports_search_and_filters(): void
    {
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);

        $affiliateA = Affiliate::create([
            'code' => 'CTV-A',
            'full_name' => 'CTV A',
            'phone' => '0900000001',
            'region' => 'japan',
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        $affiliateB = Affiliate::create([
            'code' => 'CTV-B',
            'full_name' => 'CTV B',
            'phone' => '0900000002',
            'region' => 'vietnam',
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        Customer::create([
            'affiliate_id' => $affiliateA->id,
            'full_name' => 'Nguyen Van A',
            'phone' => '0811111111',
            'prefecture' => 'Tokyo',
            'consultation_status' => 'new',
        ]);

        Customer::create([
            'affiliate_id' => $affiliateB->id,
            'full_name' => 'Tran Thi B',
            'phone' => '0822222222',
            'prefecture' => 'Osaka',
            'consultation_status' => 'consulting',
        ]);

        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff-customers@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STFCUS',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $this->actingAs($staff)
            ->getJson('/crm-api/customers?search=Nguyen')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'Nguyen Van A');

        $this->actingAs($staff)
            ->getJson('/crm-api/customers?consultation_status=consulting')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'Tran Thi B');

        $this->actingAs($staff)
            ->getJson('/crm-api/customers?prefecture=Tokyo')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.prefecture', 'Tokyo');

        $this->actingAs($staff)
            ->getJson('/crm-api/customers?affiliate_id=' . $affiliateB->id)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'Tran Thi B');
    }
}
