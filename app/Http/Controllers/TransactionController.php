<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function fetchTransactionHistory()
    {
        $Transactions = Transaction::all();
        return view('payment.transaction',compact('Transactions'));

    }
    
}
