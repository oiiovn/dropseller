<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\BalanceHistory;

class BalanceHistoryController extends Controller
{
    public function index()
    {
        $histories = BalanceHistory::where('user_id', Auth::id())
            ->get();
        return view('balance.history', compact('histories'));
    }
}

