<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreDeliveryRequest;
use App\Models\Crm\Delivery;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Delivery::query()->with(['order:id,order_code,affiliate_id', 'assignee:id,name']);
        CollaboratorScope::applyOrderScope($query, $request->user());

        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->string('delivery_status'));
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(StoreDeliveryRequest $request): JsonResponse
    {
        abort_if($request->user()->hasCrmRole('collaborator'), 403, 'CTV không có quyền tạo giao vận.');

        $delivery = Delivery::updateOrCreate(
            ['order_id' => $request->integer('order_id')],
            $request->validated()
        );

        return response()->json($delivery, 201);
    }

    public function show(Request $request, Delivery $delivery): JsonResponse
    {
        $this->ensureCollaboratorCanAccess($request, $delivery);

        return response()->json($delivery->load(['order', 'assignee']));
    }

    public function update(Request $request, Delivery $delivery): JsonResponse
    {
        abort_if($request->user()->hasCrmRole('collaborator'), 403, 'CTV không có quyền cập nhật giao vận.');

        $validated = $request->validate([
            'delivery_address' => ['sometimes', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'delivery_status' => ['nullable', 'in:pending,ready,shipping,delivered,failed,cancelled'],
            'shipping_fee_jpy' => ['nullable', 'numeric', 'min:0'],
            'delivered_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $delivery->update($validated);

        return response()->json($delivery->fresh());
    }

    public function destroy(Request $request, Delivery $delivery): JsonResponse
    {
        abort_if($request->user()->hasCrmRole('collaborator'), 403, 'CTV không có quyền xóa giao vận.');

        $delivery->delete();

        return response()->json(['message' => 'Delivery deleted']);
    }

    private function ensureCollaboratorCanAccess(Request $request, Delivery $delivery): void
    {
        if (! $request->user()->hasCrmRole('collaborator')) {
            return;
        }

        $delivery->loadMissing('order');
        abort_if(
            $delivery->order?->affiliate_id !== $request->user()->affiliate_id,
            403,
            'Không có quyền truy cập giao vận này.'
        );
    }
}
