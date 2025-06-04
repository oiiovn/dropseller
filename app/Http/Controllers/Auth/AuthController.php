<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the authentication forms.
     */
    public function next_page()
    {
        return view('auth.form_all_auth');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
{

    // dd($request->all());
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => $request->referral_code,
        ]);

        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                \App\Models\Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                ]);
            }
        }

        // Tạo token sau khi đăng ký
        $token = $user->createToken('auth_token')->plainTextToken;

        return redirect()->route('login')->with('message', 'Bạn đã đăng kí thành công. Vui lòng Đăng nhập.')
            ->with('token', $token);
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Bạn đã đăng kí thất bại. Vui lòng thử lại']);
    }
}

    

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $credentials = $request->only('email', 'password');
    
        if (!$token = JWTAuth::attempt($credentials)) {
            return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
        }
        $user = JWTAuth::user();
        return view('index', [
            'user' => $user,
            'token' => $token,
        ]);
    }
    

    /**
     * Logout the currently authenticated user.
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully!'], 200);
    }


    public function showQrModal()
    {
        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập
        $referralCode = $user->referral_code; // Lấy mã referral_code

        return view('payment.naptien', compact('referralCode'));
    }

}
