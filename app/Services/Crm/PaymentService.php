<?php

namespace App\Services\Crm;

use App\Models\Crm\Order;
use App\Models\Crm\OrderStatusHistory;
use App\Models\Crm\Payment;
use App\Models\Crm\PaymentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    private const PAYMENT_TYPE_LABELS = [
        'deposit' => 'Cọc',
        'partial' => 'Thanh toán',
        'full' => 'Tất toán',
    ];

    public function __construct(
        private readonly DebtAlertService $debtAlertService,
        private readonly CommissionService $commissionService,
        private readonly CrmNotificationService $notificationService,
        private readonly OrderAutoCompleteService $orderAutoCompleteService,
    ) {
    }

    public function submit(array $payload, int $userId): Payment
    {
        return DB::transaction(function () use ($payload, $userId) {
            $payload['discount_jpy'] = (float) ($payload['discount_jpy'] ?? 0);
            $this->assertValidDiscount($payload['order_id'], $payload['discount_jpy']);

            $payload['payment_status'] = 'pending';
            $payload['approval_status'] = 'pending';
            $payload['recorded_by'] = $userId;

            $payment = Payment::create($payload);

            PaymentHistory::create([
                'payment_id' => $payment->id,
                'from_status' => null,
                'to_status' => $payment->payment_status,
                'changed_by' => $userId,
                'note' => $this->buildPaymentNote($payment, 'Gửi duyệt kế toán'),
            ]);

            $payment = $payment->fresh(['recorder:id,name', 'approver:id,name', 'order:id,order_code']);
            $this->notificationService->paymentSubmitted($payment, User::findOrFail($userId));

            return $payment;
        });
    }

    public function approve(Payment $payment, int $userId): Payment
    {
        if ($payment->approval_status !== 'pending') {
            throw ValidationException::withMessages([
                'payment' => 'Thanh toán này không ở trạng thái chờ duyệt.',
            ]);
        }

        return DB::transaction(function () use ($payment, $userId) {
            $payment->update([
                'approval_status' => 'approved',
                'payment_status' => 'received',
                'approved_by' => $userId,
                'approved_at' => now(),
                'rejection_note' => null,
            ]);

            PaymentHistory::create([
                'payment_id' => $payment->id,
                'from_status' => 'pending',
                'to_status' => 'received',
                'changed_by' => $userId,
                'note' => 'Kế toán xác nhận thanh toán',
            ]);

            $this->applyDiscountIfNeeded($payment->fresh());
            $this->applyFinancialImpact($payment->fresh(), $userId);

            $payment = $payment->fresh(['recorder:id,name', 'approver:id,name', 'order:id,order_code,order_status']);
            $this->notificationService->paymentApproved($payment, User::findOrFail($userId));

            return $payment;
        });
    }

    public function reject(Payment $payment, int $userId, string $note): Payment
    {
        if ($payment->approval_status !== 'pending') {
            throw ValidationException::withMessages([
                'payment' => 'Thanh toán này không ở trạng thái chờ duyệt.',
            ]);
        }

        $note = trim($note);
        if ($note === '') {
            throw ValidationException::withMessages([
                'rejection_note' => 'Vui lòng nhập lý do từ chối.',
            ]);
        }

        return DB::transaction(function () use ($payment, $userId, $note) {
            $payment->update([
                'approval_status' => 'rejected',
                'payment_status' => 'failed',
                'rejection_note' => $note,
            ]);

            PaymentHistory::create([
                'payment_id' => $payment->id,
                'from_status' => 'pending',
                'to_status' => 'failed',
                'changed_by' => $userId,
                'note' => "Kế toán từ chối — {$note}",
            ]);

            $payment = $payment->fresh(['recorder:id,name', 'approver:id,name', 'order:id,order_code']);
            $this->notificationService->paymentRejected($payment, User::findOrFail($userId), $note);

            return $payment;
        });
    }

    /** @deprecated Use submit() then approve() */
    public function collect(array $payload, int $userId): Payment
    {
        return $this->submit($payload, $userId);
    }

    private function applyFinancialImpact(Payment $payment, int $userId): Order
    {
        $order = $payment->order()->with('debt')->firstOrFail();
        $debt = $order->debt;

        if ($debt) {
            $totalPaid = $this->approvedAmountForOrder($order);
            $outstanding = max((float) $debt->total_payable_jpy - $totalPaid, 0);

            $debt->update([
                'total_paid_jpy' => $totalPaid,
                'outstanding_jpy' => $outstanding,
                'debt_status' => $outstanding > 0 ? 'partial' : 'paid',
                'last_collected_at' => $payment->payment_date,
            ]);

            $order->update([
                'payment_status' => $outstanding > 0 ? 'partial' : 'paid',
                'debt_status' => $outstanding > 0 ? 'unpaid' : 'cleared',
            ]);
        } else {
            $outstanding = 0;
        }

        $outstanding = $outstanding ?? 0;
        $order = $this->syncOrderStatusAfterPayment($order->fresh(), $payment, $userId);
        $order = $this->orderAutoCompleteService->tryComplete(
            $order->fresh(),
            User::findOrFail($userId),
            $this->buildAutoCompleteNote($payment)
        );

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $order->order_status,
            'to_status' => $order->order_status,
            'changed_by' => $userId,
            'note' => $this->buildPaymentNote($payment, 'Kế toán đã xác nhận'),
        ]);

        $this->debtAlertService->syncOverdueDebts();

        return $order;
    }

    public function approvedAmountForOrder(Order $order): float
    {
        return (float) $order->payments()->where('approval_status', 'approved')->sum('amount_jpy');
    }

    private function buildPaymentNote(Payment $payment, ?string $prefix = null): string
    {
        $typeLabel = self::PAYMENT_TYPE_LABELS[$payment->payment_type] ?? 'Thanh toán';
        $amount = number_format((float) $payment->amount_jpy, 0, '.', ',');
        $discount = (float) ($payment->discount_jpy ?? 0);

        $note = ($prefix ? "{$prefix}: " : 'Ghi nhận ') . "{$typeLabel}: {$amount}円";
        if ($discount > 0) {
            $note .= ' · Giảm giá: '.number_format($discount, 0, '.', ',').'円';
        }
        if ($payment->notes) {
            $note .= " — {$payment->notes}";
        }

        return $note;
    }

    private function applyDiscountIfNeeded(Payment $payment): void
    {
        $discount = (float) ($payment->discount_jpy ?? 0);
        if ($discount <= 0) {
            return;
        }

        $order = $payment->order()->firstOrFail();
        $order->update(['discount_jpy' => $discount]);
        app(OrderService::class)->syncFinancials($order->fresh());
    }

    private function assertValidDiscount(int $orderId, float $discount): void
    {
        if ($discount <= 0) {
            return;
        }

        $order = Order::query()->with('items')->findOrFail($orderId);
        $subtotal = (float) ($order->items->sum(fn ($item) => (float) $item->line_total_jpy) ?: $order->subtotal_jpy);

        if ($subtotal <= 0) {
            throw ValidationException::withMessages([
                'discount_jpy' => 'Không thể áp dụng giảm giá cho đơn không có tiền hàng.',
            ]);
        }

        if ($discount > $subtotal) {
            throw ValidationException::withMessages([
                'discount_jpy' => 'Giảm giá không được vượt quá tiền hàng ('.number_format($subtotal, 0, '.', ',').'円).',
            ]);
        }
    }

    private function syncOrderStatusAfterPayment(Order $order, Payment $payment, int $userId): Order
    {
        $fromStatus = $order->order_status;
        $toStatus = $this->resolveOrderStatusAfterPayment($fromStatus, $payment->payment_type);

        if (! $toStatus || $toStatus === $fromStatus) {
            return $order;
        }

        $order->update(['order_status' => $toStatus]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $userId,
            'note' => $this->buildAutoStatusNote($payment, $fromStatus, $toStatus),
        ]);

        return $order->fresh();
    }

    private function resolveOrderStatusAfterPayment(string $currentStatus, string $paymentType): ?string
    {
        if (in_array($paymentType, ['deposit', 'partial'], true)) {
            if (in_array($currentStatus, ['new', 'consulting', 'confirmed', 'deposit_pending'], true)) {
                return 'deposit_paid';
            }
        }

        return null;
    }

    private function buildAutoCompleteNote(Payment $payment): string
    {
        $typeLabel = self::PAYMENT_TYPE_LABELS[$payment->payment_type] ?? 'Thanh toán';

        return "Tự động hoàn thành sau khi kế toán xác nhận {$typeLabel}";
    }

    private function buildAutoStatusNote(Payment $payment, string $fromStatus, string $toStatus): string
    {
        $typeLabel = self::PAYMENT_TYPE_LABELS[$payment->payment_type] ?? 'Thanh toán';

        return "Tự động chuyển {$fromStatus} → {$toStatus} sau khi kế toán xác nhận {$typeLabel}";
    }
}
