<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StorePaymentRequest;
use App\Models\Crm\Order;
use App\Rules\JapaneseCharacters;
use App\Services\Crm\AuditLogService;
use App\Services\Crm\CommissionService;
use App\Services\Crm\OrderService;
use App\Services\Crm\OrderWorkflowService;
use App\Services\Crm\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CommissionService $commissionService,
        private readonly OrderWorkflowService $orderWorkflowService,
        private readonly PaymentService $paymentService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Order::query()
            ->with(['customer:id,full_name,phone', 'affiliate:id,full_name,code']);

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        } elseif ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->integer('affiliate_id'));
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->string('order_status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('order_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('affiliate', function ($affiliateQuery) use ($search) {
                        $affiliateQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $orders = $query->latest()->paginate($perPage);
        $orders->getCollection()->transform(function (Order $order) {
            return $this->orderService->syncFinancials($order);
        });

        return response()->json($orders);
    }

    public function show(int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        $model = $this->orderService->syncFinancials($model);

        return response()->json($model->load([
            'customer',
            'affiliate',
            'creator:id,name',
            'items.product',
            'payments.recorder:id,name',
            'payments.approver:id,name',
            'debt',
            'delivery',
            'commission',
            'statusHistories.changer:id,name',
            'auditLogs.user:id,name',
        ]));
    }

    public function update(Request $request, int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        if ($model->trashed()) {
            throw ValidationException::withMessages([
                'order' => 'Không thể sửa đơn đã xóa. Hãy khôi phục trước.',
            ]);
        }

        $validated = $request->validate([
            'order_status' => ['sometimes', 'in:new,consulting,confirmed,deposit_pending,deposit_paid,waiting_delivery,packaged,delivering,delivered,completed,cancelled,refunded'],
            'payment_status' => ['sometimes', 'in:unpaid,partial,paid,refunded'],
            'notes' => ['nullable', 'string'],
            'status_note' => ['nullable', 'string'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_date_from' => ['nullable', 'date'],
            'delivery_date_to' => ['nullable', 'date', 'after_or_equal:delivery_date_from'],
            'delivery_time_slot' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'shipping_mode' => ['nullable', 'in:boxed,direct_ship'],
            'battery_capacity' => ['nullable', 'string', 'max:100'],
            'facebook_url' => ['nullable', 'string', 'max:500'],
            'shipping_fee_jpy' => ['nullable', 'numeric', 'min:0'],
            'vehicle_chassis_number' => ['nullable', 'string', 'max:120'],
            'vehicle_owner_name_vi' => ['nullable', 'string', 'max:255'],
            'vehicle_owner_name_ja' => ['nullable', 'string', 'max:255', new JapaneseCharacters()],
            'vehicle_owner_postal_code' => ['nullable', 'string', 'max:20'],
            'vehicle_owner_phone' => ['nullable', 'string', 'max:30'],
            'vehicle_owner_address' => ['nullable', 'string'],
        ]);

        $old = $model->toArray();
        $statusChanged = false;

        if (isset($validated['order_status'])) {
            $model = $this->orderWorkflowService->transition(
                $model,
                $validated['order_status'],
                $request->user(),
                $validated['status_note'] ?? null
            );
            $statusChanged = true;
            unset($validated['order_status'], $validated['status_note']);
        }

        if (! empty($validated)) {
            if (array_key_exists('delivery_date_from', $validated)) {
                $validated['delivery_date'] = $validated['delivery_date_from'];
            }
            $model->update($validated);
            $model = $this->orderService->syncFinancials($model->fresh());
        }

        if ($model->order_status === 'completed') {
            $this->commissionService->generateFromOrder($model->fresh());
        } elseif ($statusChanged && in_array($model->order_status, ['cancelled', 'refunded'], true)) {
            $this->commissionService->reverseForOrder($model->fresh());
        } elseif ($statusChanged) {
            $this->commissionService->refreshAffiliateMetrics($model->affiliate_id);
        }

        $this->auditLogService->write($request->user(), 'orders', 'updated', $model, $old, $model->toArray());

        return response()->json($model->fresh()->load([
            'statusHistories.changer:id,name',
            'auditLogs.user:id,name',
        ]));
    }

    public function destroy(Request $request, int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        if ($model->trashed()) {
            throw ValidationException::withMessages([
                'order' => 'Đơn hàng đã được xóa trước đó.',
            ]);
        }

        $old = $model->toArray();
        $model->delete();
        $this->auditLogService->write($request->user(), 'orders', 'deleted', $model, $old, []);

        return response()->json(['message' => 'Đã xóa mềm đơn hàng.']);
    }

    public function restore(Request $request, int $order): JsonResponse
    {
        $model = Order::onlyTrashed()->findOrFail($order);
        $old = ['deleted_at' => $model->deleted_at?->toIso8601String()];

        $model->restore();
        $model = $this->orderService->syncFinancials($model->fresh());

        $this->auditLogService->write($request->user(), 'orders', 'restored', $model, $old, $model->toArray());

        return response()->json($model->load(['customer', 'affiliate']));
    }

    public function transitions(Request $request, int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        if ($model->trashed()) {
            return response()->json([
                'current_status' => $model->order_status,
                'allowed_transitions' => [],
            ]);
        }

        return response()->json([
            'current_status' => $model->order_status,
            'allowed_transitions' => $this->orderWorkflowService->allowedTransitions($request->user(), $model),
        ]);
    }

    public function storePayment(StorePaymentRequest $request, int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        if ($model->trashed()) {
            throw ValidationException::withMessages([
                'order' => 'Không thể ghi nhận thanh toán cho đơn đã xóa.',
            ]);
        }

        $validated = $request->validated();
        $validated['order_id'] = $model->id;
        $validated['customer_id'] = $model->customer_id;

        $payment = $this->paymentService->submit($validated, $request->user()->id);
        $this->auditLogService->write($request->user(), 'payments', 'created', $payment, [], $payment->toArray());

        return response()->json($payment, 201);
    }

    public function auditTrail(int $order): JsonResponse
    {
        $model = $this->findOrder($order);

        return response()->json([
            'status_histories' => $model->statusHistories()->with('changer:id,name')->get(),
            'audit_logs' => $model->auditLogs()->with('user:id,name')->get(),
        ]);
    }

    private function findOrder(int $id): Order
    {
        return Order::withTrashed()->findOrFail($id);
    }
}
