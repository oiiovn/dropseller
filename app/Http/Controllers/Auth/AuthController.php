<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Mews\Captcha\Facades\Captcha;

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
        // Kiểm tra rate limit - giới hạn 5 lần đăng ký mỗi phút từ một IP
        $this->checkRateLimit($request, 'register', 5, 1);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'captcha' => 'required|captcha', // Thêm xác thực CAPTCHA
            ]);

            // Tạo mã referral_code ngẫu nhiên
            $referralCode = $this->generateReferralCode();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => $referralCode,
            ]);

            // Tạo token sau khi đăng ký
            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect()->route('login')->with('message', 'Bạn đã đăng kí thành công. Vui lòng Đăng nhập.')
                ->with('token', $token);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Đăng ký thất bại: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Bạn đã đăng kí thất bại. Vui lòng thử lại'])->withInput();
        }
    }

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        // Kiểm tra rate limit - giới hạn 10 lần đăng nhập mỗi phút từ một IP
        $this->checkRateLimit($request, 'login', 10, 1);

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'captcha' => 'required|captcha', // Thêm xác thực CAPTCHA
            ]);
            
            $credentials = $request->only('email', 'password');
        
            if (!$token = JWTAuth::attempt($credentials)) {
                // Tăng số lần thất bại để phát hiện brute force
                $this->incrementFailedAttempts($request);
                return redirect()->back()->withErrors(['error' => 'Email hoặc mật khẩu không chính xác']);
            }
            
            // Xóa đếm số lần thất bại khi thành công
            RateLimiter::clear($this->throttleKey($request, 'failed'));
            
            $user = JWTAuth::user();
            return view('index', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Đăng nhập thất bại: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại sau.'])->withInput();
        }
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

    /**
     * Check rate limiting for specific actions
     */
    private function checkRateLimit(Request $request, $action, $maxAttempts, $decayMinutes)
    {
        $key = $this->throttleKey($request, $action);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'error' => "Quá nhiều yêu cầu. Vui lòng thử lại sau {$seconds} giây."
            ]);
        }

        RateLimiter::hit($key, $decayMinutes * 60);
    }

    /**
     * Get the throttle key for the given request.
     */
    private function throttleKey(Request $request, $action)
    {
        return Str::lower($action . '|' . $request->ip());
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts(Request $request)
    {
        $key = $this->throttleKey($request, 'failed');
        
        // Khóa tạm thời sau 5 lần thất bại liên tiếp
        if (RateLimiter::attempts($key) >= 5) {
            $this->checkRateLimit($request, 'failed', 5, 10); // Khóa 10 phút
        } else {
            RateLimiter::hit($key, 10 * 60); // Lưu thông tin thất bại trong 10 phút
        }
    }
    
    /**
     * Generate a unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
    
    /**
     * Refresh captcha
     */
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => Captcha::img('math')]);
    }
}
