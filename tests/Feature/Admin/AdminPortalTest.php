<?php

namespace Tests\Feature\Admin;

use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_api_and_collaborator_cannot(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $collaboratorRole = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM999',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $collaborator = User::create([
            'name' => 'CTV',
            'email' => 'ctv@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTV999',
        ]);
        $collaborator->crmRoles()->attach($collaboratorRole->id);

        $this->actingAs($admin)->getJson('/admin-api/dashboard')->assertOk();
        $this->actingAs($collaborator)->getJson('/admin-api/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($collaborator)->get('/admin')->assertForbidden();
    }

    public function test_admin_is_redirected_from_crm_home_to_admin(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-home@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMHOME',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $this->actingAs($admin)->get('/crm')->assertRedirect('/admin');
    }

    public function test_admin_can_still_open_crm_subpages(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-crm-orders@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMORD',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $this->actingAs($admin)->get('/crm/orders')->assertOk();
    }

    public function test_admin_can_assign_collaborator_to_existing_user(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM999',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'phone' => '0901234567',
            'password' => bcrypt('password'),
            'referral_code' => 'STF999',
        ]);

        $response = $this->actingAs($admin)->postJson('/admin-api/collaborators', [
            'user_id' => $staff->id,
            'code' => 'CTV001',
            'area' => 'Tokyo',
            'region' => 'japan',
        ]);

        $response->assertCreated()
            ->assertJsonPath('affiliate.code', 'CTV001')
            ->assertJsonPath('affiliate.region', 'japan')
            ->assertJsonPath('user.email', 'staff@test.com');

        $staff->refresh();
        $this->assertNotNull($staff->affiliate_id);
        $this->assertTrue($staff->hasCrmRole('collaborator'));
    }

    public function test_admin_can_update_user_password_and_delete_user(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM999',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $target = User::create([
            'name' => 'Target User',
            'email' => 'target@test.com',
            'password' => bcrypt('old-password'),
            'referral_code' => 'TGT999',
        ]);

        $this->actingAs($admin)->putJson("/admin-api/users/{$target->id}", [
            'name' => 'Target Updated',
            'email' => 'target-updated@test.com',
            'password' => 'new-password',
        ])->assertOk()->assertJsonPath('name', 'Target Updated');

        $target->refresh();
        $this->assertSame('target-updated@test.com', $target->email);
        $this->assertTrue(Hash::check('new-password', $target->password));

        $this->actingAs($admin)->deleteJson("/admin-api/users/{$target->id}")
            ->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->actingAs($admin)->deleteJson("/admin-api/users/{$admin->id}")
            ->assertStatus(422);
    }

    public function test_admin_user_list_supports_search_and_filters(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $collaboratorRole = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin Filter',
            'email' => 'admin-filter@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMFIL',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $staff = User::create([
            'name' => 'Nguyen Van Staff',
            'email' => 'staff-filter@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STFFIL',
            'phone' => '0901111222',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $affiliate = \App\Models\Crm\Affiliate::create([
            'code' => 'CTV-FILTER',
            'full_name' => 'Tran Thi CTV',
            'phone' => '0903333444',
            'region' => 'japan',
            'email' => 'ctv-filter@test.com',
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        $collaborator = User::create([
            'name' => 'Tran Thi CTV',
            'email' => 'ctv-filter@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTVFIL',
            'affiliate_id' => $affiliate->id,
        ]);
        $collaborator->crmRoles()->attach($collaboratorRole->id);

        $this->actingAs($admin)
            ->getJson('/admin-api/users?search=CTV-FILTER')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'ctv-filter@test.com');

        $this->actingAs($admin)
            ->getJson('/admin-api/users?role=staff')
            ->assertOk()
            ->assertJsonPath('data.0.email', 'staff-filter@test.com');

        $this->actingAs($admin)
            ->getJson('/admin-api/users?affiliate_region=japan')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.affiliate.code', 'CTV-FILTER');

        $this->actingAs($admin)
            ->getJson('/admin-api/users?affiliate_link=none')
            ->assertOk()
            ->assertJsonMissingPath('data.0.affiliate.code');
    }

    public function test_admin_can_list_payments_from_crm_and_admin_api(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-payments@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADM-PAY',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $this->actingAs($admin)->getJson('/crm-api/payments')->assertOk();
        $this->actingAs($admin)->getJson('/admin-api/payments')->assertOk();
    }
}
