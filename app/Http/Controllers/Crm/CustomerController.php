<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreCustomerRequest;
use App\Models\Crm\Customer;
use App\Services\Crm\AuditLogService;
use App\Support\Crm\CollaboratorScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()->with(['affiliate:id,full_name,code']);
        CollaboratorScope::applyAffiliateScope($query, $request->user());

        if ($request->filled('consultation_status')) {
            $query->where('consultation_status', $request->string('consultation_status'));
        }

        if ($request->filled('prefecture')) {
            $query->where('prefecture', 'like', '%' . $request->string('prefecture') . '%');
        }

        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->integer('affiliate_id'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('full_name', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('prefecture', 'like', $search)
                    ->orWhere('city', 'like', $search)
                    ->orWhereHas('affiliate', function ($affiliateQuery) use ($search) {
                        $affiliateQuery->where('code', 'like', $search)
                            ->orWhere('full_name', 'like', $search);
                    });
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->latest()->paginate($perPage));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $this->authorize('create', Customer::class);

        $payload = $request->validated();
        $payload['affiliate_id'] = CollaboratorScope::enforceAffiliateId(
            $request->user(),
            $payload['affiliate_id'] ?? null
        );

        $customer = Customer::create($payload);
        $this->auditLogService->write($request->user(), 'customers', 'created', $customer, [], $customer->toArray());

        return response()->json($customer, 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        return response()->json($customer->load(['affiliate:id,full_name,code', 'orders']));
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'affiliate_id' => ['nullable', 'exists:affiliates,id'],
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'google_maps_url' => ['nullable', 'url', 'max:500'],
            'prefecture' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'preferred_bicycle_line' => ['nullable', 'string', 'max:120'],
            'consultation_status' => ['nullable', 'in:new,consulting,confirmed,cancelled'],
            'consultation_history' => ['nullable', 'string'],
            'purchase_interest' => ['nullable', 'string'],
        ]);

        if ($request->user()->hasCrmRole('collaborator')) {
            unset($validated['affiliate_id']);
        }

        $old = $customer->toArray();
        $customer->update($validated);
        $this->auditLogService->write($request->user(), 'customers', 'updated', $customer, $old, $customer->toArray());

        return response()->json($customer);
    }

    public function destroy(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $old = $customer->toArray();
        $customer->delete();
        $this->auditLogService->write($request->user(), 'customers', 'deleted', $customer, $old, []);

        return response()->json(['message' => 'Customer deleted']);
    }
}
