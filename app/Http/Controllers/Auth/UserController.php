<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use App\Models\Shop;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Show the authentication forms.
     */
    public function GetUser()
    {
        try {
            // Lấy thông tin user từ JWT
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
    
            // Truyền dữ liệu user sang view 'header'
            return view('header', compact('user'));
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is missing'], 401);
        }
    }
    public function Get_all()
    {
        // Lấy tất cả user
        $users = User::all();
    
        foreach ($users as $user) {
            // Lấy tất cả các shop của user
            $shops = Shop::where('user_id', $user->id)->get();
    
            foreach ($shops as $shop) {
                // Lấy tất cả đơn hàng bị trễ của shop (chưa thanh toán và quá hạn 1 ngày)
                $orders_unpaiddd = Order::where('shop_id', $shop->id)
                    ->where('payment_status', 'Chưa thanh toán')
                    ->where('created_at', '<', Carbon::now()->subDay()) // Điều kiện quá hạn 1 ngày
                    ->get();
                $orders_unpaiddd->count();
          
                // Gán danh sách đơn hàng bị trễ vào shop
                $shop->orders_unpaid = $orders_unpaiddd;
            }
            // Gán danh sách shop vào user
            $user->shops = $shops;
        }
        return view('user.profie_user', compact('users'));
    }
}
