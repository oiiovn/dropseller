<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Order;

class ProfileController extends Controller
{
    public function viewProfile()
{
    $user = Auth::user();
    $shops = Shop::where('user_id', $user->id)->get();

    foreach ($shops as $shop) {
        $shop->revenue = Order::where('shop_id', $shop->shop_id)->sum('total_bill'); 
        $orders = Order::where('shop_id', $shop->shop_id)->get();
    }
    return view('profile.profile', compact('user', 'shops'));
}

    
    

}
