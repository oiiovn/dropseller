<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Pin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pin::query()->orderBy('bike_line')->orderBy('sort_order')->orderBy('name');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        } elseif ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('bike_line')) {
            $query->where('bike_line', $request->string('bike_line'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', $search)
                    ->orWhere('bike_line', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $perPage = min(max((int) $request->input('per_page', 100), 1), 200);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $pin = Pin::create($this->validatePin($request));

        return response()->json($pin, 201);
    }

    public function show(Pin $pin): JsonResponse
    {
        return response()->json($pin);
    }

    public function update(Request $request, Pin $pin): JsonResponse
    {
        $pin->update($this->validatePin($request, true));

        return response()->json($pin->fresh());
    }

    public function destroy(Pin $pin): JsonResponse
    {
        $pin->delete();

        return response()->json(['message' => 'Pin deleted']);
    }

    private function validatePin(Request $request, bool $isUpdate = false): array
    {
        $sometimes = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$sometimes, 'string', 'max:100'],
            'bike_line' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
