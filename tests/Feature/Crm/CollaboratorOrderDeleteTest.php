<?php

namespace Tests\Feature\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Models\Crm\Product;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorOrderDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function seedCollaboratorContext(): array
    {
        $collaboratorRole = Role::create(['name' => 'collaborator', 'display_name' => 'CTV', 'guard_name' => 'web']);
        $affiliate = Affiliate::create(['code' => 'CTV-DEL', 'full_name' => 'CTV Delete', 'phone' => '0901230777']);
        $customer = Customer::create([
            'affiliate_id' => $affiliate->id,
            'full_name' => 'Customer Delete',
            'phone' => '0812345777',
        ]);
        $product = Product::create([
            'sku' => 'BIKE-DEL',
            'name' => 'Delete Bike',
            'base_cost_jpy' => 30000,
            'recommended_price_jpy' => 45000,
        ]);

        $collaborator = User::create([
            'name' => 'CTV Delete',
            'email' => 'ctvdelete@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'CTVDEL',
            'affiliate_id' => $affiliate->id,
        ]);
        $collaborator->crmRoles()->attach($collaboratorRole->id);

        return compact('collaborator', 'affiliate', 'customer', 'product');
    }

    public function test_collaborator_can_delete_order_before_packaging_and_payment(): void
    {
        ['collaborator' => $collaborator, 'affiliate' => $affiliate, 'customer' => $customer, 'product' => $product] = $this->seedCollaboratorContext();

        $orderId = $this->actingAs($collaborator)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 45000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->assertCreated()->json('id');

        $this->actingAs($collaborator)
            ->deleteJson("/crm-api/orders/{$orderId}")
            ->assertOk();

        $this->assertSoftDeleted('crm_orders', ['id' => $orderId]);
    }

    public function test_collaborator_cannot_delete_order_in_packaging(): void
    {
        ['collaborator' => $collaborator, 'affiliate' => $affiliate, 'customer' => $customer] = $this->seedCollaboratorContext();

        $order = Order::create([
            'order_code' => 'HA-DEL-01',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'waiting_delivery',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 45000,
            'subtotal_jpy' => 45000,
        ]);

        $this->actingAs($collaborator)
            ->deleteJson("/crm-api/orders/{$order->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['order']);
    }

    public function test_collaborator_cannot_delete_order_with_pending_payment(): void
    {
        ['collaborator' => $collaborator, 'affiliate' => $affiliate, 'customer' => $customer] = $this->seedCollaboratorContext();

        $order = Order::create([
            'order_code' => 'HA-DEL-02',
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'order_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_amount_jpy' => 45000,
            'subtotal_jpy' => 45000,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'amount_jpy' => 10000,
            'payment_type' => 'deposit',
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'payment_status' => 'pending',
            'approval_status' => 'pending',
            'recorded_by' => $collaborator->id,
        ]);

        $this->actingAs($collaborator)
            ->deleteJson("/crm-api/orders/{$order->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['order']);
    }

    public function test_orders_index_includes_can_delete_for_collaborator(): void
    {
        ['collaborator' => $collaborator, 'affiliate' => $affiliate, 'customer' => $customer, 'product' => $product] = $this->seedCollaboratorContext();

        $this->actingAs($collaborator)->postJson('/crm-api/orders', [
            'customer_id' => $customer->id,
            'affiliate_id' => $affiliate->id,
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price_jpy' => 45000,
                'unit_cost_jpy' => 30000,
            ]],
        ])->assertCreated();

        $response = $this->actingAs($collaborator)->getJson('/crm-api/orders');
        $response->assertOk();
        $this->assertTrue(collect($response->json('data'))->first()['can_delete']);
    }
}
