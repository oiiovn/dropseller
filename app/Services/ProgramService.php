<?php
namespace App\Services;

use App\Models\Shop;
use App\Models\Program;
use App\Models\ProgramShop;

class ProgramService
{
    public static function getUnregisteredProgramsForUser($user)
    {
        $userShopIds = Shop::where('user_id', $user->id)->pluck('shop_id')->toArray();
        $programs_all = Program::where(function ($query) use ($userShopIds) {
            foreach ($userShopIds as $shopId) {
                $query->orWhereJsonContains('shops', $shopId);
            }
        })->get();

        $registered = ProgramShop::whereIn('shop_id', $userShopIds)->get()->groupBy('program_id');
        $allShops = Shop::whereIn('shop_id', $userShopIds)->get()->keyBy('shop_id');
        $programShops = [];

        foreach ($programs_all as $program) {
            $shopIds = $program->shops ?? [];
            foreach ($shopIds as $shopId) {
                if (isset($allShops[$shopId])) {
                    $isRegistered = isset($registered[$program->id]) &&
                        $registered[$program->id]->contains('shop_id', $shopId);
                    if (!$isRegistered) {
                        $programShops[$program->id][] = [
                            'shop_id' => $shopId,
                            'shop_name' => $allShops[$shopId]->shop_name,
                            'is_registered' => false,
                        ];
                    }
                }
            }
        }

        return $programShops;
    }
}
