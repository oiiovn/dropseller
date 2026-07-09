<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAppearanceSetting;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppearanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_and_update_appearance_settings(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::create([
            'name' => 'Admin Theme',
            'email' => 'admintheme@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMTHE',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $this->actingAs($admin)->getJson('/admin-api/settings/appearance')
            ->assertOk()
            ->assertJsonPath('background_color', AdminAppearanceSetting::DEFAULTS['background_color'])
            ->assertJsonPath('sidebar_background_color', AdminAppearanceSetting::DEFAULTS['sidebar_background_color']);

        $this->actingAs($admin)->putJson('/admin-api/settings/appearance', [
            'background_color' => '#111827',
            'sidebar_background_color' => '#1f2937',
            'sidebar_text_color' => '#f9fafb',
            'sidebar_active_color' => '#059669',
            'header_background_color' => '#ffffff',
        ])->assertOk()
            ->assertJsonPath('background_color', '#111827')
            ->assertJsonPath('sidebar_active_color', '#059669');

        $this->assertDatabaseHas('admin_appearance_settings', [
            'background_color' => '#111827',
            'sidebar_active_color' => '#059669',
        ]);
    }

    public function test_crm_user_can_read_shared_appearance_settings(): void
    {
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff', 'guard_name' => 'web']);
        $staff = User::create([
            'name' => 'Staff Theme',
            'email' => 'stafftheme@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'STFTHE',
        ]);
        $staff->crmRoles()->attach($staffRole->id);

        $this->actingAs($staff)->getJson('/crm-api/settings/appearance')
            ->assertOk()
            ->assertJsonPath('background_color', AdminAppearanceSetting::DEFAULTS['background_color']);
    }
}
