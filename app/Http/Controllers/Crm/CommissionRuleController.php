<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CommissionRule;
use App\Services\Crm\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionRuleController extends Controller
{
    public function __construct(private readonly CommissionService $commissionService)
    {
    }
    public function index(): JsonResponse
    {
        $rules = CommissionRule::query()
            ->withCount('affiliates')
            ->with(['affiliates:id,code,full_name'])
            ->orderBy('priority')
            ->paginate(20);

        return response()->json($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRule($request);

        $affiliateIds = $validated['affiliate_ids'] ?? [];
        unset($validated['affiliate_ids']);

        $rule = CommissionRule::create($validated);
        $this->commissionService->syncRuleAffiliates($rule, $affiliateIds);

        return response()->json($this->loadRule($rule), 201);
    }

    public function show(CommissionRule $commissionRule): JsonResponse
    {
        return response()->json($this->loadRule($commissionRule));
    }

    public function update(Request $request, CommissionRule $commissionRule): JsonResponse
    {
        $validated = $this->validateRule($request, true);

        $affiliateIds = array_key_exists('affiliate_ids', $validated)
            ? ($validated['affiliate_ids'] ?? [])
            : null;
        unset($validated['affiliate_ids']);

        $commissionRule->update($validated);

        if ($affiliateIds !== null) {
            $this->commissionService->syncRuleAffiliates($commissionRule, $affiliateIds);
        }

        return response()->json($this->loadRule($commissionRule->fresh()));
    }

    public function destroy(CommissionRule $commissionRule): JsonResponse
    {
        $commissionRule->delete();

        return response()->json(['message' => 'Commission rule deleted']);
    }

    private function validateRule(Request $request, bool $isUpdate = false): array
    {
        $sometimes = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$sometimes, 'string', 'max:255'],
            'product_category' => ['nullable', 'string', 'max:100'],
            'min_order_amount_jpy' => ['nullable', 'numeric', 'min:0'],
            'max_order_amount_jpy' => ['nullable', 'numeric', 'min:0'],
            'min_affiliate_sales_jpy' => ['nullable', 'numeric', 'min:0'],
            'max_affiliate_sales_jpy' => ['nullable', 'numeric', 'min:0'],
            'rate_percent' => [$sometimes, 'numeric', 'min:0', 'max:100'],
            'priority' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'affiliate_ids' => ['nullable', 'array'],
            'affiliate_ids.*' => ['integer', 'exists:affiliates,id'],
        ]);
    }

    private function loadRule(CommissionRule $rule): CommissionRule
    {
        return $rule->load(['affiliates:id,code,full_name'])->loadCount('affiliates');
    }
}
