<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Debt;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Debt::query()->with(['order:id,order_code,affiliate_id', 'customer:id,full_name']);
        CollaboratorScope::applyOrderScope($query, $request->user());

        if ($request->filled('debt_status')) {
            $query->where('debt_status', $request->string('debt_status'));
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        abort_if($request->user()->hasCrmAnyRole(['collaborator', 'packaging']), 403, 'Không có quyền tạo công nợ.');

        $validated = $request->validate([
            'order_id' => ['required', 'exists:crm_orders,id', 'unique:crm_debts,order_id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'total_payable_jpy' => ['required', 'numeric', 'min:0'],
            'total_paid_jpy' => ['nullable', 'numeric', 'min:0'],
            'outstanding_jpy' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'debt_status' => ['nullable', 'in:unpaid,partial,paid,overdue'],
            'notes' => ['nullable', 'string'],
        ]);

        $debt = Debt::create($validated);

        return response()->json($debt, 201);
    }

    public function show(Request $request, Debt $debt): JsonResponse
    {
        $this->ensureCollaboratorCanAccess($request, $debt);

        return response()->json($debt->load(['order', 'customer']));
    }

    public function update(Request $request, Debt $debt): JsonResponse
    {
        abort_if($request->user()->hasCrmAnyRole(['collaborator', 'packaging']), 403, 'Không có quyền cập nhật công nợ.');

        $validated = $request->validate([
            'total_paid_jpy' => ['nullable', 'numeric', 'min:0'],
            'outstanding_jpy' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'debt_status' => ['nullable', 'in:unpaid,partial,paid,overdue'],
            'notes' => ['nullable', 'string'],
        ]);

        $debt->update($validated);

        return response()->json($debt->fresh());
    }

    public function destroy(Request $request, Debt $debt): JsonResponse
    {
        abort_if($request->user()->hasCrmAnyRole(['collaborator', 'packaging']), 403, 'Không có quyền xóa công nợ.');

        $debt->delete();

        return response()->json(['message' => 'Debt deleted']);
    }

    private function ensureCollaboratorCanAccess(Request $request, Debt $debt): void
    {
        if (! $request->user()->hasCrmRole('collaborator')) {
            return;
        }

        $debt->loadMissing('order');
        abort_if(
            $debt->order?->affiliate_id !== $request->user()->affiliate_id,
            403,
            'Không có quyền truy cập công nợ này.'
        );
    }
}
