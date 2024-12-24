<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
   function Getorder(){
    return view('order.order');
   }
   
   function order_si(){
      return view('order.order_si');
     }
}
