<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PaymentController extends Controller
{
    public function Getnaptien(){
        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập
        if (!$user) {
            abort(403, 'Bạn cần đăng nhập để truy cập.');
        }

        return view('payment.naptien', [
            'referralCode' => $user->referral_code, // Truyền referral_code vào view
        ]);
    }
}
