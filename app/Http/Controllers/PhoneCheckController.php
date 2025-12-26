<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CheckSo;
use App\Models\User;
use App\Jobs\CheckPhoneJob;

class PhoneCheckController extends Controller
{
    public function form()
    {
        $user = auth()->user();
        $records = CheckSo::where('referral_code', $user->referral_code)
            ->latest()
            ->get();

        return view('check-sdt.check-so-dien-thoai', compact('records'));
    }


    public function submitUsername(Request $request)
    {
        $username = trim($request->username);

        if (empty($username)) {
            return redirect()->back()->with('error', 'Vui lòng nhập username.');
        }

        // Lấy user đang đăng nhập
        $user = auth()->user();
        $referralCode = $user->referral_code ?? null;

        // Gửi request mua fanpage
        $postResponse = Http::asForm()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
            ])
            ->withCookies([
                'user_login' => '32f4744fe9893659c458b50aa4c8c34dd97478e0877bcf00e8d8430144df871d'
            ], 'hawksocia.com')
            ->post('https://hawksocia.com/ajaxs/client/buyStorefanpage.php', [
                'coupon' => '',
                'token' => '32f4744fe9893659c458b50aa4c8c34dd97478e0877bcf00e8d8430144df871d',
                'url' => $username,
                'new_name' => $username,
                'id' => '437',
            ]);

        if (!$postResponse->successful()) {
            return redirect()->back()->with('error', 'Gửi yêu cầu thất bại.');
        }

        // Ghi vào DB
        $record = CheckSo::create([
            'username' => $username,
            'referral_code' => $referralCode,
            'phone' => null,
            'exists' => false,
            'status' => 'pending',
            'type' => 'check',
        ]);

        // Dispatch job xử lý sau 15s
        CheckPhoneJob::dispatch($record->id)->delay(now()->addSeconds(15));

        return redirect()->route('check_so_dt')->with('success', 'Đã gửi và đang kiểm tra.');
    }
}

