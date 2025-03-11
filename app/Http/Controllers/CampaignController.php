<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function campaign(request $request)
    {
        $original_price = 100000;
        $percentage = $request->campaign_percentage ;
        $shop_discount = $request->shop_discount ;
        $price_after_discount = $original_price / 100 * $shop_discount ; 
        return view('tools.campaign');
    }
}
 