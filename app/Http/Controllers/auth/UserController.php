<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
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
    
            // Truyền dữ liệu user sang view 'header'
            return view('header', compact('user'));
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // Token đã hết hạn
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            // Token không hợp lệ
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // Token không được tìm thấy
            return response()->json(['error' => 'Token is missing'], 401);
        }
    }
    
  
}
