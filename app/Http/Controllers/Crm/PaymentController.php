<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StorePaymentRequest;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Policies\Crm\OrderPolicy;
use App\Services\Crm\AuditLogService;
use App\Services\Crm\PaymentService;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query()->with([
            'order:id,order_code,affiliate_id',
            'customer:id,full_name',
            'recorder:id,name',
            'approver:id,name',
        ]);
        CollaboratorScope::applyOrderScope($query, $request->user());

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->string('approval_status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('reference_code', 'like', $search)
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_code', 'like', $search))
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('full_name', 'like', $search));
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->latest()->paginate($perPage));
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $order = Order::query()->findOrFail($request->input('order_id'));
        $this->authorize('create', Payment::class);
        $this->authorize('view', $order);

        if ($request->user()->hasCrmRole('packaging')) {
            abort_unless(
                in_array($order->order_status, OrderPolicy::FULFILLMENT_STATUSES, true),
                403,
                'Chỉ ghi nhận thanh toán cho đơn đang xử lý giao vận.'
            );
        }

        if ($request->user()->hasCrmRole('collaborator') && $order->affiliate_id !== $request->user()->affiliate_id) {
            abort(403, 'Không có quyền ghi nhận thanh toán cho đơn này.');
        }

        $payment = $this->paymentService->submit($request->validated(), $request->user()->id);
        $this->auditLogService->write($request->user(), 'payments', 'created', $payment, [], $payment->toArray());

        return response()->json($payment, 201);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        return response()->json($payment->load(['order', 'customer', 'histories', 'recorder:id,name', 'approver:id,name']));
    }

    public function approve(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('approve', $payment);

        $old = $payment->toArray();
        $payment = $this->paymentService->approve($payment, $request->user()->id);
        $this->auditLogService->write($request->user(), 'payments', 'approved', $payment, $old, $payment->toArray());

        return response()->json($payment);
    }

    public function reject(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('reject', $payment);

        $validated = $request->validate([
            'rejection_note' => ['required', 'string', 'min:1', 'max:500'],
        ]);

        $old = $payment->toArray();
        $payment = $this->paymentService->reject($payment, $request->user()->id, trim($validated['rejection_note']));
        $this->auditLogService->write($request->user(), 'payments', 'rejected', $payment, $old, $payment->toArray());

        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('update', $payment);

        abort_if($request->user()->hasCrmAnyRole(['collaborator', 'packaging']), 403, 'Không có quyền cập nhật thanh toán.');

        $validated = $request->validate([
            'reference_code' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $payment->toArray();
        $payment->update($validated);
        $this->auditLogService->write($request->user(), 'payments', 'updated', $payment, $old, $payment->toArray());

        return response()->json($payment->fresh());
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('delete', $payment);

        abort_if($request->user()->hasCrmAnyRole(['collaborator', 'packaging']), 403, 'Không có quyền xóa thanh toán.');

        $old = $payment->toArray();
        $payment->delete();
        $this->auditLogService->write($request->user(), 'payments', 'deleted', $payment, $old, []);

        return response()->json(['message' => 'Payment deleted']);
    }
}
