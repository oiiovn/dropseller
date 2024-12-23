<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
   function Getorder(){
    return view('order.order');
   }
}
