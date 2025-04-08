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
        $totalTopup = Transaction::where('description', 'LIKE', "%$userCode%")
            ->where('bank', 'MBB')
            ->where('type', '=', 'IN')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        $totalPaid = Transaction::where('account_number', $userCode)
            ->where('bank', 'DROP')
            ->where('type', '=', 'OUT')
            ->whereBetween('transaction_date',  [
                Carbon::parse($startDate)->addDays(),
                Carbon::parse($endDate)->addDays()
            ])
            ->sum('amount');
        $totalPaid_ads = Transaction::where('account_number', $userCode)
            ->where('bank', 'ADS')
            ->where('type', '=', 'OUT')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        $totalCanceled = Transaction::where('account_number', $userCode)
            ->where('bank', 'DROP')
            ->where('type', '=', 'IN')
            ->whereBetween('transaction_date', [
                Carbon::parse($startDate)->addDays(19),
                Carbon::parse($endDate)->addDays(19)
            ])
            ->sum('amount');
        $Ending_balance = $totalTopup - $totalPaid - $totalPaid_ads + $totalCanceled;
        $total_chi = $totalPaid + $totalPaid_ads - $totalCanceled;
        return view('settlement.monthly', compact('month', 'totalTopup', 'totalPaid', 'totalCanceled', 'Ending_balance', 'totalPaid_ads', 'total_chi'));
    }
}
