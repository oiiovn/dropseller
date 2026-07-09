<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreProductRequest;
use App\Models\Crm\Product;
use App\Services\Crm\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', $search)
                    ->orWhere('sku', 'like', $search);
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        $this->auditLogService->write($request->user(), 'products', 'created', $product, [], $product->toArray());

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'base_cost_jpy' => ['nullable', 'numeric', 'min:0'],
            'recommended_price_jpy' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $product->toArray();
        $product->update($validated);
        $this->auditLogService->write($request->user(), 'products', 'updated', $product, $old, $product->toArray());

        return response()->json($product);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $old = $product->toArray();
        $product->delete();
        $this->auditLogService->write($request->user(), 'products', 'deleted', $product, $old, []);

        return response()->json(['message' => 'Product deleted']);
    }
}
