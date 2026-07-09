<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreOrderRequest;
use App\Http\Requests\Crm\StorePaymentRequest;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Policies\Crm\OrderPolicy;
use App\Rules\JapaneseCharacters;
use App\Services\Crm\AuditLogService;
use App\Services\Crm\CommissionService;
use App\Services\Crm\OrderDeletionService;
use App\Services\Crm\OrderService;
use App\Services\Crm\OrderWorkflowService;
use App\Services\Crm\PaymentService;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CommissionService $commissionService,
        private readonly OrderWorkflowService $orderWorkflowService,
        private readonly PaymentService $paymentService,
        private readonly AuditLogService $auditLogService,
        private readonly OrderDeletionService $orderDeletionService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::query()->with([
            'customer:id,full_name,phone',
            'affiliate:id,full_name,code',
            'debt:id,order_id,total_paid_jpy,outstanding_jpy,total_payable_jpy',
        ])->withExists(['payments as has_effective_payment' => function ($paymentQuery) {
            $paymentQuery->whereIn('approval_status', ['pending', 'approved']);
        }]);
        CollaboratorScope::applyAffiliateScope($query, $request->user());

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->string('order_status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('shipping_mode')) {
            $query->where('shipping_mode', $request->string('shipping_mode'));
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

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);
        $orders = $query->latest()->paginate($perPage);
        $orders->getCollection()->transform(function (Order $order) use ($request) {
            $order = $this->orderService->syncFinancials($order);

            if ($request->user()->hasCrmRole('collaborator')) {
                $order->setAttribute(
                    'can_delete',
                    $this->orderDeletionService->canCollaboratorDelete($order)
                );
            }

            return $order;
        });

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $payload = $request->validated();
        $payload['affiliate_id'] = CollaboratorScope::enforceAffiliateId(
            $request->user(),
            $payload['affiliate_id'] ?? null
        );

        $customer = Customer::findOrFail($payload['customer_id']);
        if ($request->user()->hasCrmRole('collaborator') && $customer->affiliate_id !== $request->user()->affiliate_id) {
            throw ValidationException::withMessages([
                'customer_id' => 'Khách hàng không thuộc phạm vi CTV của bạn.',
            ]);
        }

        $order = $this->orderService->create($payload, $request->user()->id);
        $this->auditLogService->write($request->user(), 'orders', 'created', $order, [], $order->toArray());

        return response()->json($order, 201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order = $this->orderService->syncFinancials($order);

        return response()->json($order->load([
            'customer',
            'affiliate',
            'items.product',
            'payments.recorder:id,name',
            'payments.approver:id,name',
            'debt',
            'delivery',
            'commission',
            'statusHistories.changer:id,name',
        ]));
    }

    public function storePayment(StorePaymentRequest $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $this->authorize('create', Payment::class);

        if ($request->user()->hasCrmRole('packaging')) {
            abort_unless(
                in_array($order->order_status, OrderPolicy::FULFILLMENT_STATUSES, true),
                403,
                'Chỉ ghi nhận thanh toán cho đơn đang xử lý giao vận.'
            );
        }

        if ($request->user()->hasCrmRole('collaborator') && $order->affiliate_id !== $request->user()->affiliate_id) {
            throw ValidationException::withMessages([
                'order_id' => 'Không có quyền ghi nhận thanh toán cho đơn này.',
            ]);
        }

        $validated = $request->validated();
        $validated['order_id'] = $order->id;
        $validated['customer_id'] = $order->customer_id;

        $payment = $this->paymentService->submit($validated, $request->user()->id);
        $this->auditLogService->write($request->user(), 'payments', 'created', $payment, [], $payment->toArray());

        return response()->json($payment, 201);
    }

    public function transitions(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json([
            'current_status' => $order->order_status,
            'allowed_transitions' => $this->orderWorkflowService->allowedTransitions($request->user(), $order),
        ]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'order_status' => ['sometimes', 'in:new,consulting,confirmed,deposit_pending,deposit_paid,waiting_delivery,packaged,delivering,delivered,completed,cancelled,refunded'],
            'payment_status' => ['sometimes', 'in:unpaid,partial,paid,refunded'],
            'notes' => ['nullable', 'string'],
            'status_note' => ['nullable', 'string'],
            'vehicle_chassis_number' => ['nullable', 'string', 'max:120'],
            'vehicle_owner_name_vi' => ['nullable', 'string', 'max:255'],
            'vehicle_owner_name_ja' => ['nullable', 'string', 'max:255', new JapaneseCharacters()],
            'vehicle_owner_postal_code' => ['nullable', 'string', 'max:20'],
            'vehicle_owner_phone' => ['nullable', 'string', 'max:30'],
            'vehicle_owner_address' => ['nullable', 'string'],
        ]);

        if ($request->user()->hasCrmRole('collaborator')) {
            unset($validated['payment_status']);
        }

        if ($request->user()->hasCrmRole('packaging')) {
            unset($validated['payment_status'], $validated['notes']);
        }

        if (! $request->user()->hasCrmAnyRole(['admin', 'staff', 'collaborator'])) {
            unset(
                $validated['vehicle_chassis_number'],
                $validated['vehicle_owner_name_vi'],
                $validated['vehicle_owner_name_ja'],
                $validated['vehicle_owner_postal_code'],
                $validated['vehicle_owner_phone'],
                $validated['vehicle_owner_address'],
            );
        }

        $old = $order->toArray();
        $statusChanged = false;

        if (isset($validated['order_status'])) {
            $order = $this->orderWorkflowService->transition(
                $order,
                $validated['order_status'],
                $request->user(),
                $validated['status_note'] ?? null
            );
            $statusChanged = true;
            unset($validated['order_status'], $validated['status_note']);
        }

        if (! empty($validated)) {
            $order->update($validated);
        }

        if ($order->order_status === 'completed') {
            $this->commissionService->generateFromOrder($order->fresh());
        } elseif ($statusChanged && in_array($order->order_status, ['cancelled', 'refunded'], true)) {
            $this->commissionService->reverseForOrder($order->fresh());
        } elseif ($statusChanged) {
            $this->commissionService->refreshAffiliateMetrics($order->affiliate_id);
        }

        $this->auditLogService->write($request->user(), 'orders', 'updated', $order, $old, $order->toArray());

        return response()->json($order->fresh()->load(['statusHistories.changer:id,name']));
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        if ($request->user()->hasCrmRole('collaborator')) {
            if ($request->user()->affiliate_id !== $order->affiliate_id) {
                abort(403, 'Không có quyền xóa đơn này.');
            }

            $this->orderDeletionService->assertCollaboratorCanDelete($order);
        } else {
            $this->authorize('delete', $order);
        }

        $old = $order->toArray();
        $order->delete();
        $this->auditLogService->write($request->user(), 'orders', 'deleted', $order, $old, []);

        return response()->json(['message' => 'Đã xóa đơn hàng.']);
    }
}
