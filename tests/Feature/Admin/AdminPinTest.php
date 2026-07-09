<?php

namespace Tests\Feature\Admin;

use App\Models\Crm\Pin;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPinTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_pins(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::create([
            'name' => 'Admin Pin',
            'email' => 'adminpin@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMPIN',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $create = $this->actingAs($admin)->postJson('/admin-api/pins', [
            'name' => '8.7Ah Panasonic',
            'bike_line' => 'Panasonic Gyutto',
            'description' => 'Pin lithium 36V',
            'is_active' => true,
            'sort_order' => 1,
        ])->assertCreated()
            ->assertJsonPath('name', '8.7Ah Panasonic')
            ->assertJsonPath('bike_line', 'Panasonic Gyutto')
            ->assertJsonPath('description', 'Pin lithium 36V');

        $pinId = $create->json('id');

        $this->actingAs($admin)->putJson("/admin-api/pins/{$pinId}", [
            'name' => '10Ah Panasonic',
            'description' => 'Pin cập nhật',
            'is_active' => false,
            'sort_order' => 2,
        ])->assertOk()->assertJsonPath('name', '10Ah Panasonic');

        $this->actingAs($admin)->getJson('/admin-api/pins')
            ->assertOk()
            ->assertJsonPath('data.0.name', '10Ah Panasonic');

        $this->actingAs($admin)->deleteJson("/admin-api/pins/{$pinId}")
            ->assertOk();

        $this->assertDatabaseMissing('crm_pins', ['id' => $pinId]);
    }

    public function test_crm_api_lists_active_pins_for_order_form(): void
    {
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staffpin@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STFPIN',
        ]);

        Pin::create(['name' => 'Active Pin', 'description' => 'Dùng được', 'is_active' => true, 'sort_order' => 1]);
        Pin::create(['name' => 'Hidden Pin', 'description' => 'Tắt', 'is_active' => false, 'sort_order' => 2]);

        $this->actingAs($user)->getJson('/crm-api/pins?active_only=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Active Pin');
    }
}
