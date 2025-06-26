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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Rules\Captcha as CaptchaRule;
use Mews\Captcha\Facades\Captcha;

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
        // Kiểm tra rate limit - giới hạn 3 lần đăng ký mỗi 5 phút từ một IP
        $this->checkRateLimit($request);
        
        try {
            // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::beginTransaction();
            
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'referral_code' => ['nullable', 'string', 'exists:' . User::class . ',referral_code'],
                'captcha' => ['required', new CaptchaRule()], // Sử dụng custom rule thay vì 'captcha'
            ]);

            // Tạo mã referral_code
            $referralCode = $this->generateReferralCode();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => $referralCode,
            ]);

            $sellerRole = \App\Models\Role::where('slug', 'seller')->first();
            if ($sellerRole) {
                $user->roles()->syncWithoutDetaching([$sellerRole->id]);
            }

            // Commit transaction
            DB::commit();

            // Gửi sự kiện đã đăng ký
            event(new Registered($user));

            // Chuyển hướng về trang đăng nhập với thông báo
            return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Đăng ký thất bại: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại sau.'])->withInput();
        }
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

    /**
     * Check rate limiting for registration
     */
    private function checkRateLimit(Request $request)
    {
        $key = 'register|' . $request->ip();
        $maxAttempts = 3; // 3 lần thử trong 5 phút
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'error' => "Quá nhiều yêu cầu đăng ký. Vui lòng thử lại sau {$seconds} giây."
            ]);
        }

        RateLimiter::hit($key, 5 * 60); // Đếm 5 phút
    }
    
    /**
     * Refresh captcha
     */
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => Captcha::img('default')]);
    }
    
    /**
     * Generate image selection captcha
     */
    public function generateImageCaptcha()
    {
        // Danh sách các loại hình ảnh có thể yêu cầu người dùng chọn
        $imageTypes = ['car', 'bicycle', 'tree', 'traffic_light', 'bus', 'house'];
        
        // Chọn ngẫu nhiên loại hình cần chọn
        $targetType = $imageTypes[array_rand($imageTypes)];
        
        // Tạo một tập hợp 9 hình ảnh (3x3 grid) với một số hình thuộc loại đích
        $images = $this->generateImageGrid($targetType);
        
        // Lưu thông tin captcha vào session
        session(['image_captcha' => [
            'target_type' => $targetType,
            'correct_indices' => $images['correct_indices'],
            'expires_at' => now()->addMinutes(5), // Captcha hết hạn sau 5 phút
        ]]);
        
        return response()->json([
            'captcha_type' => 'image_selection',
            'images' => $images['grid'],
            'target_type' => $targetType,
            'instruction' => "Vui lòng chọn tất cả hình ảnh chứa {$targetType}",
        ]);
    }
    
    /**
     * Validate image selection captcha
     */
    public function validateImageCaptcha(array $selectedIndices): bool
    {
        $captchaData = session('image_captcha');
        
        if (!$captchaData || now()->isAfter($captchaData['expires_at'])) {
            return false; // Captcha đã hết hạn hoặc không tồn tại
        }
        
        // So sánh các chỉ số đã chọn với các chỉ số chính xác
        $correctIndices = $captchaData['correct_indices'];
        
        // Kiểm tra nếu người dùng đã chọn đúng tất cả các hình và không chọn thừa
        return count(array_diff($correctIndices, $selectedIndices)) === 0 && 
               count(array_diff($selectedIndices, $correctIndices)) === 0;
    }
    
    /**
     * Generate grid of images for captcha
     */
    private function generateImageGrid(string $targetType): array
    {
        // Đường dẫn tới thư mục chứa hình ảnh captcha
        // (Giả định rằng bạn đã có thư mục public/captcha-images với các thư mục con cho mỗi loại)
        $imagePath = 'captcha-images/';
        
        // Danh sách hình ảnh khác loại để trộn vào
        $otherTypes = ['car', 'bicycle', 'tree', 'traffic_light', 'bus', 'house'];
        $otherTypes = array_diff($otherTypes, [$targetType]);
        
        $grid = [];
        $correctIndices = [];
        
        // Tạo ngẫu nhiên 3-5 hình ảnh thuộc loại đích
        $targetCount = rand(3, 5);
        $positions = array_rand(range(0, 8), $targetCount);
        if (!is_array($positions)) {
            $positions = [$positions];
        }
        
        // Điền grid với 9 hình ảnh
        for ($i = 0; $i < 9; $i++) {
            if (in_array($i, $positions)) {
                // Thêm hình thuộc loại đích
                $grid[] = $imagePath . $targetType . '/' . rand(1, 10) . '.jpg';
                $correctIndices[] = $i;
            } else {
                // Thêm hình ngẫu nhiên từ loại khác
                $randomType = $otherTypes[array_rand($otherTypes)];
                $grid[] = $imagePath . $randomType . '/' . rand(1, 10) . '.jpg';
            }
        }
        
        return [
            'grid' => $grid,
            'correct_indices' => $correctIndices
        ];
    }
}
