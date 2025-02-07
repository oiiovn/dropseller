<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Shop;

class PaymentController extends Controller
{
    public function Getnaptien()
    {
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
    public function thanhtoan()
    {
        $user = Auth::user(); 
        $total_amount = $user->total_amount; 
    
        $shops = Shop::where('user_id', $user->id)->get(); 
        $allOrders = [];
    
        foreach ($shops as $shop) {
            $orders = Order::where('shop_id', $shop->shop_id)
                ->where('payment_status', 'Chưa thanh toán')
                ->orderBy('created_at', 'asc')
                ->get();
    
            $allOrders = array_merge($allOrders, $orders->toArray()); 
        }
    
        foreach ($allOrders as $orderData) {
            $transactionId = $this->generateUniqueTransactionId();
            $uniqueId = $this->generateUniqueId(); 
            $order = Order::find($orderData['id']); 
    
            if (!$order) {
                continue; 
            }
            if ($total_amount >= $order->total_bill) {
                $order->payment_status = 'Đã thanh toán';
                $order->transaction_id = $transactionId; 
                $order->save(); 
                $total_amount -= $order->total_bill;
                $user->total_amount = $total_amount;
                $user->save();
                Transaction::create([
                    'id' =>  $uniqueId, 
                    'bank' => 'DROP',
                    'account_number' => $user->referral_code,
                    'transaction_date' => now(),
                    'transaction_id' => $transactionId,
                    'description' => $user->referral_code .' '. $order->order_code,
                    'type' => 'OUT',
                    'amount' => $order->total_bill,
                ]);
            } 
        }
    
        return redirect()->route('order_si')->with('success', 'Thanh toán thành công!');
    }
    

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'FT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists()); 
        return $transactionId;
    }
    private function generateUniqueId($length = 8)
    {
        do {
            $id = random_int(pow(10, $length - 1), pow(10, $length) - 1);
        } while (Transaction::where('id', $id)->exists());
    
        return $id;
    }
    
    
}
