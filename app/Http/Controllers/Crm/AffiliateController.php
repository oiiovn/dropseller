<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreAffiliateRequest;
use App\Models\Crm\Affiliate;
use App\Services\Crm\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Affiliate::class);

        $query = Affiliate::query()->withCount(['customers', 'orders']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('region')) {
            $query->where('region', $request->string('region'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('full_name', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhere('phone', 'like', $search);
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(StoreAffiliateRequest $request): JsonResponse
    {
        $this->authorize('create', Affiliate::class);

        $affiliate = Affiliate::create($request->validated());
        $this->auditLogService->write($request->user(), 'affiliates', 'created', $affiliate, [], $affiliate->toArray());

        return response()->json($affiliate, 201);
    }

    public function show(Affiliate $affiliate): JsonResponse
    {
        $this->authorize('view', $affiliate);

        $affiliate->loadCount(['customers', 'orders']);

        return response()->json($affiliate);
    }

    public function update(Request $request, Affiliate $affiliate): JsonResponse
    {
        $this->authorize('update', $affiliate);

        $validated = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:30', 'unique:affiliates,phone,' . $affiliate->id],
            'email' => ['nullable', 'email', 'max:255'],
            'area' => ['nullable', 'string', 'max:150'],
            'region' => ['sometimes', 'in:japan,vietnam'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $affiliate->toArray();
        $affiliate->update($validated);
        $this->auditLogService->write($request->user(), 'affiliates', 'updated', $affiliate, $old, $affiliate->toArray());

        return response()->json($affiliate);
    }

    public function destroy(Request $request, Affiliate $affiliate): JsonResponse
    {
        $this->authorize('delete', $affiliate);

        $old = $affiliate->toArray();
        $affiliate->delete();
        $this->auditLogService->write($request->user(), 'affiliates', 'deleted', $affiliate, $old, []);

        return response()->json(['message' => 'Affiliate deleted']);
    }
}
