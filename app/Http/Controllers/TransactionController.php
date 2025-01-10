<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function fetchTransactionHistory()
    {
        $userCode = Auth::user()->referral_code;
        $Transactions = Transaction::where('description', 'LIKE', "%$userCode%")->get();
        $Transaction_nap = Transaction::where('description', 'LIKE', "%$userCode%")
                                      ->where('type', '=', 'in')
                                      ->get();
    
        return view('payment.transaction', compact('Transactions', 'Transaction_nap'));
    }
    
}
