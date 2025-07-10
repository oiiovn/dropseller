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
        // $request->validate([
        //     'username' => 'required|string|max:255',
        // ]);



        $username = trim($request->username);
        // Lấy referral_code theo username
        $user = \App\Models\User::where('name', $username)->first();
        $referralCode = $user ? $user->referral_code : null;
        $postResponse = Http::asForm()
            ->withHeaders([
                'User-Agent' => 'Mozilla%2F5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F140.0.0.0%20Safari%2F537.36',
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
            'User-Agent' => 'Mozilla%2F5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F140.0.0.0%20Safari%2F537.36',
        ])->withCookies([
            'user_login' => '32f4744fe9893659c458b50aa4c8c34dd97478e0877bcf00e8d8430144df871d'
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
