<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
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
    // public function Total_amout(Request $request){
    //     $user = Auth::user();
    //     if (!$user) {
    //         abort(403, 'Bạn cần đăng nhập để truy cập.');
    //     }
    //     $userCode = $user->referral_code;
    //     $Transactions = Transaction::where('description', 'LIKE', "%$userCode%")->get();
    //     $totalAmount = $user->total_amout;
    //    if($Transactions->type == 'in'){
    //        $totalAmount += $Transactions->amount;
    //    }elseif($Transactions->type == 'out'){
    //        $totalAmount -= $Transactions->amount;
    //    }
    //     return view('header', compact('totalAmount'));
    // }
}
