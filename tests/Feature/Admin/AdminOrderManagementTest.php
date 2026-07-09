<?php

namespace Tests\Feature\Admin;

use App\Models\Crm\Affiliate;
use App\Models\Crm\AuditLog;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_update_soft_delete_and_restore_orders(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-ADM', 'full_name' => 'CTV Admin', 'phone' => '0901111222']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Admin',
            'phone' => '0811222333',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-ADM',
            'name' => 'Admin Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 50000,
        ]);

        $admin = User::create([
            'name' => 'Admin Orders',
            'email' => 'adminorders@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'ADMORD',
        ]);
        $admin->crmRoles()->attach($adminRole->id);

        $orderId = $this->actingAs($admin)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 50000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->json('id');

        $this->actingAs($admin)->getJson('/admin-api/orders')
            ->assertOk()
            ->assertJsonFragment(['order_code' => Order::find($orderId)->order_code]);

        $this->actingAs($admin)->putJson("/admin-api/orders/{$orderId}", [
            'notes' => 'Admin note updated',
            'delivery_date' => now()->toDateString(),
        ])->assertOk()->assertJsonPath('notes', 'Admin note updated');

        $this->assertDatabaseHas('crm_audit_logs', [
            'module' => 'orders',
            'event' => 'updated',
            'auditable_id' => $orderId,
        ]);

        $this->actingAs($admin)->deleteJson("/admin-api/orders/{$orderId}")
            ->assertOk();

        $this->assertSoftDeleted('crm_orders', ['id' => $orderId]);
        $this->assertDatabaseHas('crm_audit_logs', [
            'module' => 'orders',
            'event' => 'deleted',
            'auditable_id' => $orderId,
        ]);

        $this->actingAs($admin)->getJson('/admin-api/orders?trashed=1')
            ->assertOk()
            ->assertJsonFragment(['id' => $orderId]);

        $this->actingAs($admin)->postJson("/admin-api/orders/{$orderId}/restore")
            ->assertOk()
            ->assertJsonPath('id', $orderId);

        $this->assertDatabaseHas('crm_orders', [
            'id' => $orderId,
            'deleted_at' => null,
        ]);

        $this->assertTrue(
            AuditLog::where('auditable_id', $orderId)->where('event', 'restored')->exists()
        );
    }

    public function test_collaborator_cannot_access_admin_orders_api(): void
    {
        $collaboratorRole = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);
        $collaborator = User::create([
            'name' => 'CTV Orders',
            'email' => 'ctvorders@test.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTVORD',
        ]);
        $collaborator->crmRoles()->attach($collaboratorRole->id);

        $this->actingAs($collaborator)->getJson('/admin-api/orders')->assertForbidden();
    }
}
