<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CheckSo;

class PhoneCheckController extends Controller
{
    public function form()
    {
        $records = CheckSo::latest()->get();
        return view('check-sdt.check-so-dien-thoai', compact('records'));
    }

    public function submitUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
    

        $username = trim($request->username);
        // Lấy referral_code theo username
        $user = \App\Models\User::where('name', $username)->first();
        $referralCode = $user ? $user->referral_code : null;

        // 1. Gửi yêu cầu tạo đơn
        $postResponse = Http::asForm()->post('https://hawksocia.com/ajaxs/client/buyStorefanpage.php', [
            'coupon' => '',
            'token' => 'GSN6PN3TYFNDO5VW', // ← Thay bằng token web của bạn
            'url' => $username,
            'new_name' => $username,
            'id' => '437', // ID dịch vụ Fanpage
        ]);

        // 2. Lưu tạm vào DB trạng thái đang xử lý
        $record = CheckSo::create([
            'username' => $username,
            'referral_code' => $referralCode,
            'phone' => null,
            'exists' => false,
            'status' => 'pending',
            'type' => 'check',
        ]);

        // 3. Đợi vài giây cho hệ thống xử lý đơn
        sleep(15);

        // 4. Gửi GET để lấy kết quả
        $getResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
        ])->withCookies([
            'user_login' => '60d27e648da79ea1b8f9e625f22023965698bfb576f8d4f2e855af2f3e59ccfcvubui92'
        ], 'hawksocia.com')->get('https://hawksocia.com/client/store-fanpage-orders');

        if ($getResponse->successful()) {
            $html = $getResponse->body();

            // 5. Dò kết quả từ HTML
            preg_match_all(
                '/<tr>.*?<td>\d+<\/td>.*?<td>(.*?)<\/td>.*?<td><a href=".*?" target="_blank"><strong>(.*?)<\/strong><\/a><\/td>.*?<td>(.*?)<\/td>/s',
                $html,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $foundUsername = trim($match[1]);
                $foundStatus = trim($match[2]);
                $foundPhone = trim($match[3]);

                if (strtolower($foundUsername) === strtolower($username)) {
                    $record->update([
                        'phone' => $foundPhone,
                        'status' => strtolower($foundStatus),
                        'exists' => strtolower($foundStatus) === 'success',
                    ]);
                    break;
                }
            }

            // Nếu không thấy username
            if ($record->status === 'pending') {
                $record->update(['status' => 'fail']);
            }
        } else {
            $record->update(['status' => 'fail']);
        }

        return redirect()->route('check_so_dt')->with('success', 'Đã gửi và kiểm tra kết quả.');
    }
}
