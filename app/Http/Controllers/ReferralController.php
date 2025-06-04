<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $referrals = $user->referredUsers()->orderByDesc('created_at')->get();
        return view('affiliate.my-affiliates', compact('referrals'));
    }
}
