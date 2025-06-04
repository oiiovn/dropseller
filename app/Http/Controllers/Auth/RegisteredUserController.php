<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'exists:' . User::class . ',referral_code'],
        ]);
    
        // Tạo mã referral_code
        $referralCode = $this->generateReferralCode();
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => $referralCode,
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
    
        // Gửi sự kiện đã đăng ký (nếu có xử lý sự kiện như gửi email xác thực)
        event(new Registered($user));
    
        // Chuyển hướng về trang đăng nhập với thông báo
        return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
    }
    /**
     * Generate a unique referral code with the format NAP_<random_number>.
     */
    private function generateReferralCode(): string
{
    do {
        // Tạo mã gồm 5 ký tự ngẫu nhiên (chữ cái viết hoa và số)
        $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
    } while (User::where('referral_code', $code)->exists()); // Kiểm tra mã đã tồn tại chưa

    return $code;
}

    
    
}
