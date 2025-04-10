<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Notification;
use App\Traits\BalanceLoggable;
class TransactionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use BalanceLoggable;
    public function fetchTransactionHistory()
    {
        $userCode = Auth::user()->referral_code;
        $Transactions = Transaction::with('order')
            ->where('description', 'LIKE', "%$userCode%")
            ->get();
        $Transaction_nap = Transaction::with('order')
            ->where('description', 'LIKE', "%$userCode%")
            ->where('bank', 'MBB')
            ->where('type', '=', 'IN')
            ->get();
        $Transactions_Drop = Transaction::with('order')
            ->where('description', 'LIKE', "%$userCode%")
            ->where('bank', 'DROP')
            ->get();
        $Transactions_ads = Transaction::with('ads')
            ->where('description', 'LIKE', "%$userCode%")
            ->where('bank', 'ADS')
            ->get();
        return view('payment.transaction', compact('Transactions', 'Transaction_nap', 'Transactions_Drop', 'Transactions_ads'));
    }

    public function updateOrderReconciled()
    {
        $dateThreshold = Carbon::now()->subDays(1);
        $transactionId = $this->generateUniqueTransactionId();
        $uniqueId = $this->generateUniqueId();

        $transactions = Transaction::with('order')
            ->where('transaction_date', '<', $dateThreshold)
            ->whereHas('order', function ($query) {
                $query->where('reconciled', 1);
            })
            ->get();

        $updatedCount = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->order) {
                if ($transaction->amount != $transaction->order->total_bill) {
                    $amount = $transaction->amount;
                    $amount -= $transaction->order->total_bill;
                    $transaction->order->update(['reconciled' => 0]);
                    $updatedCount++;
                    Notification::create([
                        'user_id' => $transaction->order->shop->user->id,
                        'shop_id' => $transaction->order->shop_id,
                        'image' => '  https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/5d6b33d2d4816adf3390_iwkcee.jpg',
                        'title' => 'Đối soát đơn hàng',
                        'message' => 'Đơn hàng ' . $transaction->order->order_code . ' đã bị hoàn hoặc hủy. Số tiền hoàn: ' . number_format($amount) . ' VND.',
                    ]);
                    Transaction::create([
                        'id' =>  $uniqueId,
                        'bank' => 'DROP',
                        'account_number' => $transaction->order->shop_id,
                        'transaction_date' => now(),
                        'transaction_id' => $transactionId,
                        'description' => $transaction->order->shop->user->referral_code . ' Thanh toán tiền hoàn, huỷ đơn ' . $transaction->order->order_code,
                        'type' => 'IN',
                        'amount' => $amount,
                    ]);
                }
            }
        }
        return response()->json(['success' => "Đã cập nhật $updatedCount đơn hàng!"]);
    }

    public function Get_transaction_all()
    {
        $users = User::all();
        $transactionsByReferral = [];

        foreach ($users as $user) {
            $transactions = Transaction::where('description', 'LIKE', "%$user->referral_code%")
            ->where('bank', 'MBB')
                ->where('type', 'IN')
                ->get();
            $transactionsByReferral[$user->referral_code] = [
                'user' => $user,
                'transactions' => $transactions
            ];
        }

        return view('payment.transaction_all', compact('transactionsByReferral'));
    }
    public function get_all_transaction()
    {
        $users = User::all();
        $transactionsByReferral = [];
        foreach ($users as $user) {
            $transactions = Transaction::where('description', 'LIKE', "%$user->referral_code%")
                ->get();
            $transactionsByReferral[$user->referral_code] = [
                'user' => $user,
                'transactions' => $transactions
            ];
        }
        return view('payment.transaction_all', compact('transactionsByReferral'));
    }
    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'AT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $transactionId)->exists()); // Thay `Order` bằng model bạn sử dụng

        return $transactionId;
    }
    private function generateUniqueId($length = 8)
    {
        do {
            $id = random_int(pow(10, $length - 1), pow(10, $length) - 1);
        } while (Transaction::where('id', $id)->exists());

        return $id;
    }

    public function show()
    {
        $users = User::all();
        return view('naptien', compact('users'));
    }
    public function addTransaction()
    {
        $transactionId = $this->generateUniqueTransactionId();
        $uniqueId = $this->generateUniqueId();
        $amount = request('Amount');
        $referralCode = request('referral_code');
    
        $user = User::where('referral_code', $referralCode)->first();
    
        if (!$user) {
            return redirect()->back()->with('error', 'Không tìm thấy user với referral code này.');
        }
    
        $type = "IN";
        $bank = "MBB";
        $description = $referralCode . ' ADMIN Nạp tiền';
        $account_number = $referralCode;
    
        $transaction = Transaction::create([
            'id' => $uniqueId,
            'bank' => $bank,
            'account_number' => $account_number,
            'transaction_date' => now(),
            'transaction_id' => $transactionId,
            'description' => $description,
            'type' => $type,
            'amount' => $amount,
        ]);
        return redirect()->back()->with('success', 'Giao dịch đã được thêm và số dư đã cập nhật!');
    }
    
}
