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
            dd($user);
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
    
    
  
}
