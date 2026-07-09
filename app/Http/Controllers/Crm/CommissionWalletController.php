<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionSettlement;
use App\Services\Crm\CommissionService;
use App\Services\Crm\CommissionWalletService;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionWalletController extends Controller
{
    public function __construct(
        private readonly CommissionWalletService $walletService,
        private readonly CommissionService $commissionService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Commission::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'period_month' => ['nullable', 'date_format:Y-m'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'area' => ['nullable', 'string', 'max:100'],
            'unpaid_only' => ['nullable', 'boolean'],
            'has_pending_settlement' => ['nullable', 'boolean'],
        ]);

        $affiliateId = CollaboratorScope::isCollaborator($request->user())
            ? $request->user()->affiliate_id
            : ($request->integer('affiliate_id') ?: null);

        $filters = array_filter([
            'search' => $validated['search'] ?? null,
            'period_month' => $validated['period_month'] ?? null,
            'status' => $validated['status'] ?? null,
            'area' => $validated['area'] ?? null,
            'unpaid_only' => $request->boolean('unpaid_only'),
            'has_pending_settlement' => $request->boolean('has_pending_settlement'),
        ], fn ($value) => $value !== null && $value !== false && $value !== '');

        $rows = $this->walletService->listAffiliateSummaries($affiliateId, $filters);

        $totals = [
            'unpaid_balance_jpy' => (float) $rows->sum('unpaid_balance_jpy'),
            'paid_balance_jpy' => (float) $rows->sum('paid_balance_jpy'),
            'current_month_commission_jpy' => (float) $rows->sum('current_month_commission_jpy'),
            'current_month_order_count' => (int) $rows->sum('current_month_order_count'),
            'pending_settlements_count' => (int) $rows->sum('pending_settlements_count'),
            'pending_settlements_amount_jpy' => (float) $rows->sum('pending_settlements_amount_jpy'),
        ];

        $page = max((int) $request->input('page', 1), 1);
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);
        $total = $rows->count();
        $lastPage = max((int) ceil($total / $perPage), 1);

        return response()->json([
            'totals' => $totals,
            'filter' => [
                'search' => $validated['search'] ?? null,
                'period_month' => $validated['period_month'] ?? now()->format('Y-m'),
                'status' => $validated['status'] ?? null,
                'area' => $validated['area'] ?? null,
                'unpaid_only' => $request->boolean('unpaid_only'),
                'has_pending_settlement' => $request->boolean('has_pending_settlement'),
            ],
            'data' => $rows->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page' => $lastPage,
            'total' => $total,
        ]);
    }

    public function show(Request $request, Affiliate $affiliate): JsonResponse
    {
        $this->authorizeAffiliateAccess($request, $affiliate);

        $validated = $request->validate([
            'period_month' => ['nullable', 'date_format:Y-m'],
            'wallet_status' => ['nullable', 'in:pending_record,credited,pending_settlement,settled,cancelled'],
            'search' => ['nullable', 'string', 'max:100'],
            'settlement_status' => ['nullable', 'in:pending,paid'],
        ]);

        $periodMonth = $validated['period_month'] ?? now()->format('Y-m');
        $summary = $this->walletService->summaryForAffiliate($affiliate->id, $periodMonth);

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $commissionsQuery = Commission::query()
            ->with(['order.customer:id,full_name', 'commissionRule:id,name'])
            ->where('affiliate_id', $affiliate->id)
            ->where('wallet_status', '!=', 'cancelled')
            ->when($periodMonth, function ($query) use ($periodMonth) {
                $query->whereYear('credited_at', substr($periodMonth, 0, 4))
                    ->whereMonth('credited_at', substr($periodMonth, 5, 2));
            })
            ->when($validated['wallet_status'] ?? null, fn ($query, $status) => $query->where('wallet_status', $status))
            ->when($validated['search'] ?? null, function ($query) use ($validated) {
                $search = '%'.$validated['search'].'%';
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->whereHas('order', fn ($orderQuery) => $orderQuery->where('order_code', 'like', $search))
                        ->orWhereHas('order.customer', fn ($customerQuery) => $customerQuery->where('full_name', 'like', $search));
                });
            })
            ->latest('credited_at');

        $commissions = $commissionsQuery->paginate($perPage);

        $settlementsQuery = CommissionSettlement::query()
            ->where('affiliate_id', $affiliate->id)
            ->when($validated['settlement_status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($periodMonth, fn ($query) => $query->where('period_month', $periodMonth));

        $settlements = (clone $settlementsQuery)
            ->latest('period_month')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $summary,
            'commissions' => $commissions->items(),
            'commissions_current_page' => $commissions->currentPage(),
            'commissions_last_page' => $commissions->lastPage(),
            'commissions_total' => $commissions->total(),
            'settlements' => $settlements,
            'filter' => [
                'period_month' => $periodMonth,
                'wallet_status' => $validated['wallet_status'] ?? null,
                'search' => $validated['search'] ?? null,
                'settlement_status' => $validated['settlement_status'] ?? null,
            ],
        ]);
    }

    public function listSettlements(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Commission::class);

        $query = CommissionSettlement::query()->with([
            'affiliate:id,code,full_name,phone',
            'payer:id,name',
        ]);

        if (CollaboratorScope::isCollaborator($request->user())) {
            $query->where('affiliate_id', CollaboratorScope::affiliateId($request->user()));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('period_month')) {
            $query->where('period_month', $request->string('period_month'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->string('search').'%';
            $query->whereHas('affiliate', function ($builder) use ($search) {
                $builder
                    ->where('full_name', 'like', $search)
                    ->orWhere('code', 'like', $search);
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $summary = [
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'pending_amount_jpy' => (float) (clone $query)->where('status', 'pending')->sum('total_amount_jpy'),
            'paid_amount_jpy' => (float) (clone $query)->where('status', 'paid')->sum('total_amount_jpy'),
        ];

        $paginator = $query->latest('period_month')->latest('id')->paginate($perPage);

        return response()->json([
            'summary' => $summary,
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function createSettlement(Request $request, Affiliate $affiliate): JsonResponse
    {
        $this->authorizeSettlement($request);

        $validated = $request->validate([
            'period_month' => ['required', 'date_format:Y-m'],
            'note' => ['nullable', 'string'],
        ]);

        $settlement = $this->walletService->createSettlement(
            $affiliate->id,
            $validated['period_month'],
            $validated['note'] ?? null
        );

        return response()->json($settlement, 201);
    }

    public function settle(Request $request, CommissionSettlement $settlement): JsonResponse
    {
        $this->authorizeSettlement($request);

        if ($settlement->status === 'paid') {
            return response()->json(['message' => 'Phiếu quyết toán đã được thanh toán.'], 422);
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $result = $this->walletService->markSettlementPaid(
            $settlement,
            $request->user()->id,
            $validated['note'] ?? null
        );

        $this->commissionService->refreshAffiliateMetrics($settlement->affiliate_id);

        return response()->json($result);
    }

    private function authorizeAffiliateAccess(Request $request, Affiliate $affiliate): void
    {
        if ($request->user()->hasCrmAnyRole(['admin', 'staff', 'accounting'])) {
            return;
        }

        if ($request->user()->hasCrmRole('collaborator') && $request->user()->affiliate_id === $affiliate->id) {
            return;
        }

        abort(403);
    }

    private function authorizeSettlement(Request $request): void
    {
        abort_unless(
            $request->user()->hasCrmAnyRole(['admin', 'accounting']),
            403,
            'Chỉ kế toán hoặc admin mới quyết toán hoa hồng.'
        );
    }
}
