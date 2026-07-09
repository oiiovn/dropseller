<?php

namespace App\Services\Crm;

use App\Models\Crm\Debt;
use App\Models\Crm\Delivery;
use App\Models\Crm\Order;
use App\Models\Crm\OrderItem;
use App\Models\Crm\OrderStatusHistory;
use App\Models\Crm\Product;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly CrmNotificationService $notificationService)
    {
    }

    public function create(array $payload, int $userId): Order
    {
        $order = DB::transaction(function () use ($payload, $userId) {
            $confirmedAt = $payload['confirmed_at'] ?? now()->toDateString();
            $deliveryFrom = $payload['delivery_date_from'] ?? $payload['delivery_date'] ?? null;
            $deliveryTo = $payload['delivery_date_to'] ?? null;

            $order = Order::create([
                'order_code' => $this->generateUniqueOrderCode(),
                'customer_id' => $payload['customer_id'],
                'affiliate_id' => $payload['affiliate_id'],
                'created_by' => $userId,
                'order_status' => $payload['order_status'] ?? 'confirmed',
                'payment_status' => 'unpaid',
                'debt_status' => 'unpaid',
                'discount_jpy' => array_key_exists('bike_price_jpy', $payload) ? 0 : ($payload['discount_jpy'] ?? 0),
                'shipping_fee_jpy' => $payload['shipping_fee_jpy'] ?? 0,
                'notes' => $payload['notes'] ?? null,
                'facebook_url' => $payload['facebook_url'] ?? null,
                'battery_capacity' => $payload['battery_capacity'] ?? null,
                'payment_method' => $payload['payment_method'] ?? null,
                'shipping_mode' => $payload['shipping_mode'] ?? null,
                'delivery_date' => $deliveryFrom,
                'delivery_date_from' => $deliveryFrom,
                'delivery_date_to' => $deliveryTo,
                'delivery_time_slot' => $payload['delivery_time_slot'] ?? null,
                'confirmed_at' => $confirmedAt,
            ]);

            $subtotal = 0;
            $cost = 0;

            foreach ($payload['items'] as $item) {
                $lineTotal = (float) $item['unit_price_jpy'] * (int) $item['quantity'];
                $lineCost = (float) $item['unit_cost_jpy'] * (int) $item['quantity'];

                $productId = $item['product_id'] ?? null;
                if (! $productId) {
                    $product = Product::firstOrCreate(
                        ['name' => trim($item['product_name'])],
                        [
                            'sku' => 'MAN-' . strtoupper(Str::random(8)),
                            'base_cost_jpy' => $item['unit_cost_jpy'],
                            'recommended_price_jpy' => $item['unit_price_jpy'],
                            'is_active' => true,
                        ]
                    );
                    $productId = $product->id;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'unit_price_jpy' => $item['unit_price_jpy'],
                    'unit_cost_jpy' => $item['unit_cost_jpy'],
                    'line_total_jpy' => $lineTotal,
                    'line_profit_jpy' => $lineTotal - $lineCost,
                    'notes' => Arr::get($item, 'notes'),
                ]);

                $subtotal += $lineTotal;
                $cost += $lineCost;
            }

            if (array_key_exists('bike_price_jpy', $payload)) {
                $subtotal = (float) $payload['bike_price_jpy'];
                $shippingFee = (float) ($payload['shipping_fee_jpy'] ?? $order->shipping_fee_jpy ?? 0);
                $totalAmount = $subtotal + $shippingFee;

                $order->update(['shipping_fee_jpy' => $shippingFee]);

                $firstItem = $order->items()->orderBy('id')->first();
                if ($firstItem) {
                    $lineCost = (float) $firstItem->unit_cost_jpy * (int) $firstItem->quantity;
                    $firstItem->update([
                        'unit_price_jpy' => $subtotal,
                        'line_total_jpy' => $subtotal,
                        'line_profit_jpy' => $subtotal - $lineCost,
                    ]);
                }
            } else {
                $shippingFee = (float) $order->shipping_fee_jpy;
                $totalAmount = $subtotal - (float) $order->discount_jpy + $shippingFee;
            }

            $profit = $totalAmount - $cost;

            $order->update([
                'subtotal_jpy' => $subtotal,
                'total_amount_jpy' => $totalAmount,
                'total_cost_jpy' => $cost,
                'total_profit_jpy' => $profit,
            ]);

            Debt::create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'total_payable_jpy' => $totalAmount,
                'total_paid_jpy' => 0,
                'outstanding_jpy' => $totalAmount,
                'due_date' => Arr::get($payload, 'debt_due_date'),
                'debt_status' => 'unpaid',
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => $order->order_status,
                'changed_by' => $userId,
                'note' => 'Đơn được tạo mới',
            ]);

            if ($deliveryFrom) {
                $customer = $order->customer()->first();
                $deliveryNote = $payload['delivery_time_slot'] ?? null;
                if ($deliveryTo && $deliveryTo !== $deliveryFrom) {
                    $rangeNote = "Dự kiến giao: {$deliveryFrom} → {$deliveryTo}";
                    $deliveryNote = $deliveryNote ? "{$deliveryNote} · {$rangeNote}" : $rangeNote;
                }

                Delivery::create([
                    'order_id' => $order->id,
                    'delivery_address' => $customer?->address ?: '—',
                    'scheduled_at' => $deliveryFrom,
                    'delivery_status' => 'pending',
                    'shipping_fee_jpy' => $order->shipping_fee_jpy,
                    'note' => $deliveryNote,
                ]);
            }

            return $order->load(['items.product', 'debt', 'delivery']);
        });

        $this->notificationService->orderCreated($order, User::findOrFail($userId));

        return $order;
    }

    public function syncFinancials(Order $order): Order
    {
        $order->loadMissing(['items', 'debt']);

        $itemsSubtotal = $order->items->sum(fn (OrderItem $item) => (float) $item->line_total_jpy);
        $subtotal = $itemsSubtotal > 0 ? $itemsSubtotal : (float) $order->subtotal_jpy;
        $shipping = (float) $order->shipping_fee_jpy;
        $discount = (float) $order->discount_jpy;

        if ($discount >= $subtotal && $subtotal > 0) {
            $discount = 0;
        }

        $totalAmount = $discount > 0
            ? $subtotal - $discount + $shipping
            : $subtotal + $shipping;

        $cost = $order->items->sum(fn (OrderItem $item) => (float) $item->unit_cost_jpy * (int) $item->quantity);

        $order->update([
            'subtotal_jpy' => $subtotal,
            'discount_jpy' => $discount,
            'total_amount_jpy' => $totalAmount,
            'total_cost_jpy' => $cost,
            'total_profit_jpy' => $totalAmount - $cost,
        ]);

        if ($order->debt) {
            $paid = app(PaymentService::class)->approvedAmountForOrder($order);
            $order->debt->update([
                'total_payable_jpy' => $totalAmount,
                'total_paid_jpy' => $paid,
                'outstanding_jpy' => max($totalAmount - $paid, 0),
            ]);
        }

        return $order->fresh([
            'items.product',
            'debt',
            'customer:id,full_name,phone',
            'affiliate:id,full_name,code',
            'delivery',
        ]);
    }

    private function generateUniqueOrderCode(): string
    {
        do {
            $code = 'HA-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::withTrashed()->where('order_code', $code)->exists());

        return $code;
    }
}
