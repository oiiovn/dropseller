<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function fetchTransactionHistory()
    {
        return view('payment.transaction');

    }
}
