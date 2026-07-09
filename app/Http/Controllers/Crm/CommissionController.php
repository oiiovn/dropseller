<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreCommissionPayoutRequest;
use App\Models\Crm\Commission;
use App\Models\Crm\Order;
use App\Services\Crm\CommissionService;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function __construct(private readonly CommissionService $commissionService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Commission::class);

        $query = Commission::query()->with([
            'affiliate:id,full_name,code',
            'order:id,order_code,total_amount_jpy,customer_id',
            'order.customer:id,full_name',
            'commissionRule:id,name',
        ]);
        CollaboratorScope::applyAffiliateScope($query, $request->user());

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Commission::class);

        $validated = $request->validate([
            'order_id' => ['required', 'exists:crm_orders,id'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $commission = $this->commissionService->generateFromOrder($order, (float) ($validated['commission_rate'] ?? 5));

        return response()->json($commission, 201);
    }

    public function show(Commission $commission): JsonResponse
    {
        $this->authorize('view', $commission);

        return response()->json($commission->load(['affiliate', 'order.customer', 'commissionRule', 'settlement', 'payouts']));
    }

    public function update(Request $request, Commission $commission): JsonResponse
    {
        $this->authorize('update', $commission);

        $action = $request->string('action')->value();

        if ($action === 'approve') {
            return response()->json($this->commissionService->approve($commission, $request->user()->id));
        }

        return response()->json($commission);
    }

    public function destroy(Commission $commission): JsonResponse
    {
        $this->authorize('delete', $commission);

        $commission->delete();

        return response()->json(['message' => 'Commission deleted']);
    }

    public function payout(StoreCommissionPayoutRequest $request, Commission $commission): JsonResponse
    {
        $this->authorize('update', $commission);

        $result = $this->commissionService->payout($commission, $request->validated(), $request->user()->id);

        return response()->json($result);
    }
}
