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

        $baseQuery = Transaction::where(function ($query) use ($userCode) {
            $query->whereRaw("description REGEXP '[[:<:]]{$userCode}[[:>:]]'")
                ->orWhere('account_number', $userCode);
        });

        // Tìm giao dịch có referral_code trong description HOẶC account_number
        $Transactions = (clone $baseQuery)
            ->with('order')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $Transaction_nap = (clone $baseQuery)
            ->with('order')
            ->whereIn('bank', ['MBB', 'ACB'])
            ->where('type', '=', 'IN')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $Transactions_Drop = (clone $baseQuery)
            ->with('order')
            ->where('bank', 'DROP')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $Transactions_ads = (clone $baseQuery)
            ->with('ads')
            ->where('bank', 'ADS')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
        
        // Aliases for view compatibility
        $Bill_Si = $Transactions_Drop; // Giao dịch đơn sỉ
        $Naptien = $Transaction_nap;   // Nạp tiền
        $ADS = $Transactions_ads;       // Chi tiêu ADS
        $Dich_Vu = (clone $baseQuery)
            ->with('order')
            ->whereIn('bank', ['QTD', 'V9999'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $total_in = (clone $baseQuery)->where('type', 'IN')->sum('amount');
        $total_out = (clone $baseQuery)->where('type', 'OUT')->sum('amount');
        $total_ads = (clone $baseQuery)->where('type', 'OUT')->where('bank', 'ADS')->sum('amount');
        $balance = $total_in - $total_out;

        return view('payment.transaction', compact(
            'Transactions',
            'Transaction_nap',
            'Transactions_Drop',
            'Transactions_ads',
            'Bill_Si',
            'Naptien',
            'ADS',
            'Dich_Vu',
            'balance',
            'total_in',
            'total_out',
            'total_ads'
        ));
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
            // Tìm giao dịch có referral_code trong description HOẶC account_number
            $transactions = Transaction::where(function($query) use ($user) {
                $query->whereRaw("description REGEXP '[[:<:]]{$user->referral_code}[[:>:]]'")
                      ->orWhere('account_number', $user->referral_code);
            })
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
            // Tìm giao dịch có referral_code trong description HOẶC account_number
            $transactions = Transaction::where(function($query) use ($user) {
                $query->whereRaw("description REGEXP '[[:<:]]{$user->referral_code}[[:>:]]'")
                      ->orWhere('account_number', $user->referral_code);
            })->get();
            
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
    public function get_SI_transaction()
    {
        $referralCodes = [
            'UT-',
            'PUCA',
            'KHANH XUAN',
            'GO-',
            'BAO AN ',
        ];
        $dd_si = [];
        foreach ($referralCodes as $code) {
            $transactions = Transaction::whereRaw("description REGEXP '[[:<:]]{$code}[[:>:]]'")
                ->orderBy('transaction_date', 'desc')
                ->get();
            $dd_si[$code] = [
                'referral_code' => $code,
                'transactions' => $transactions,
            ];
        }
        return view('payment.transaction_si', compact('dd_si'));
    }
}
