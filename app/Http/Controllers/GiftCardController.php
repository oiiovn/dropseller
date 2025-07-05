<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    /**
     * Display the gift card printing page
     *
     * @return \Illuminate\View\View
     */
    public function inGiayTangQua()
    {
        return view('gift-card.in_giay_tang_qua');
    }
} 