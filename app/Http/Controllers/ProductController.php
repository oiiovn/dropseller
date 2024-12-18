<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
   function Getproduct(){
    return view('product.list_product');
   }
}
