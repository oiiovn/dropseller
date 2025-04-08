<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    public function monthly(Request $request)
    {
        $userCode = Auth::user()->referral_code;

        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Tổng tiền đã nạp từ ngân hàng MBB, tài khoản là mã giới thiệu
        $totalTopup = Transaction::where('description', 'LIKE', "%$userCode%")
            ->where('bank', 'MBB')
            ->where('type', '=', 'IN')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        $totalPaid = Transaction::where('account_number', $userCode)
        ->where('type', '=', 'OUT')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('amount');
        // Tổng tiền giao dịch DROP (bị huỷ/hoàn), cùng mã giới thiệu
        $totalCanceled = Transaction::where('account_number', $userCode)
            ->where('bank', 'DROP')
            ->where('type', '=', 'IN')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return view('settlement.monthly', compact('month', 'totalTopup', 'totalPaid', 'totalCanceled'));
    }
}
