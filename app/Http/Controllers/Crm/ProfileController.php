<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Affiliate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('crmRoles', 'affiliate');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'currency' => $user->currency ?: 'JPY',
            'locale' => $user->locale ?: 'vi',
            'image' => $user->image,
            'affiliate_id' => $user->affiliate_id,
            'roles' => $user->crmRoles->pluck('display_name')->values(),
            'role_names' => $user->crmRoles->pluck('name')->values(),
            'is_collaborator' => $user->hasCrmRole('collaborator'),
            'is_packaging' => $user->hasCrmRole('packaging'),
            'is_accounting' => $user->hasCrmRole('accounting'),
            'is_staff' => $user->hasCrmAnyRole(['admin', 'staff']),
            'affiliate' => $user->affiliate ? [
                'id' => $user->affiliate->id,
                'code' => $user->affiliate->code,
                'full_name' => $user->affiliate->full_name,
                'phone' => $user->affiliate->phone,
                'area' => $user->affiliate->area,
                'status' => $user->affiliate->status,
                'gross_sales_jpy' => $user->affiliate->gross_sales_jpy,
                'successful_orders' => $user->affiliate->successful_orders,
                'total_commission_jpy' => $user->affiliate->total_commission_jpy,
                'paid_commission_jpy' => $user->affiliate->paid_commission_jpy,
                'pending_commission_jpy' => $user->affiliate->pending_commission_jpy,
                'total_referred_customers' => $user->affiliate->total_referred_customers,
            ] : null,
        ]);
    }

    public function affiliate(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->affiliate_id) {
            return response()->json(['message' => 'Chưa gán CTV.'], 404);
        }

        $affiliate = Affiliate::query()
            ->withCount(['customers', 'orders'])
            ->findOrFail($user->affiliate_id);

        $this->authorize('view', $affiliate);

        return response()->json($affiliate);
    }
}
