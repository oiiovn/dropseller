<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with(['crmRoles:id,name,display_name', 'affiliate:id,code,full_name,region']);

        if ($request->boolean('available_for_collaborator') || $request->string('affiliate_link') === 'none') {
            $query->whereNull('affiliate_id');
        }

        if ($request->string('affiliate_link') === 'linked') {
            $query->whereNotNull('affiliate_id');
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhereHas('affiliate', function ($affiliateQuery) use ($search) {
                        $affiliateQuery->where('code', 'like', $search)
                            ->orWhere('full_name', 'like', $search);
                    });
            });
        }

        if ($request->filled('role')) {
            $role = $request->string('role');
            $query->whereHas('crmRoles', fn ($roleQuery) => $roleQuery->where('name', $role));
        }

        if ($request->filled('affiliate_region')) {
            $region = $request->string('affiliate_region');
            $query->whereHas('affiliate', fn ($affiliateQuery) => $affiliateQuery->where('region', $region));
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->latest()->paginate($perPage));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'affiliate_id' => ['nullable', 'exists:affiliates,id'],
            'role_names' => ['nullable', 'array'],
            'role_names.*' => ['string', Rule::exists('crm_roles', 'name')],
            'affiliate_region' => ['nullable', 'in:japan,vietnam'],
        ]);

        if (array_key_exists('role_names', $validated)) {
            $roleIds = Role::query()->whereIn('name', $validated['role_names'] ?? [])->pluck('id');
            $user->crmRoles()->sync($roleIds);
            unset($validated['role_names']);
        }

        if (array_key_exists('affiliate_region', $validated)) {
            unset($validated['affiliate_region']);
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->filled('affiliate_region') && $user->affiliate_id) {
            Affiliate::whereKey($user->affiliate_id)->update([
                'region' => $request->string('affiliate_region'),
            ]);
        }

        return response()->json($user->fresh()->load(['crmRoles:id,name,display_name', 'affiliate:id,code,full_name,region']));
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Không thể xóa tài khoản đang đăng nhập.',
            ], 422);
        }

        $user->crmRoles()->detach();
        $user->delete();

        return response()->json(['message' => 'Đã xóa tài khoản.']);
    }

    public function storeCollaborator(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'code' => ['required', 'string', 'max:50', 'unique:affiliates,code'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:affiliates,phone'],
            'area' => ['nullable', 'string', 'max:150'],
            'region' => ['required', 'in:japan,vietnam'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);

        if ($user->affiliate_id) {
            return response()->json([
                'message' => 'Tài khoản này đã được gán CTV.',
            ], 422);
        }

        $fullName = ($validated['full_name'] ?? null) ?: $user->name;
        $phone = ($validated['phone'] ?? null) ?: $user->phone;

        if (! $phone) {
            return response()->json([
                'message' => 'Vui lòng nhập số điện thoại cho CTV.',
                'errors' => ['phone' => ['Số điện thoại là bắt buộc.']],
            ], 422);
        }

        $affiliate = Affiliate::create([
            'code' => $validated['code'],
            'full_name' => $fullName,
            'phone' => $phone,
            'area' => $validated['area'] ?? null,
            'region' => $validated['region'],
            'email' => $user->email,
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        $user->update([
            'affiliate_id' => $affiliate->id,
            'phone' => $user->phone ?: $phone,
        ]);

        $collaboratorRole = Role::where('name', 'collaborator')->first();
        if ($collaboratorRole) {
            $user->crmRoles()->syncWithoutDetaching([$collaboratorRole->id]);
        }

        return response()->json([
            'affiliate' => $affiliate,
            'user' => $user->fresh()->load(['crmRoles:id,name,display_name', 'affiliate:id,code,full_name,region']),
        ], 201);
    }
}
