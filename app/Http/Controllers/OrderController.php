<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\OrderTiktokExport;
use App\Imports\OrderTiktokimport;


use Maatwebsite\Excel\Facades\Excel;


class OrderController extends Controller
{
   function Getorder(){
    return view('order.order');
   }
   
   function order_si(){
      return view('order.order_si');
     }
     public function exportOrders()
     {
         // Xuất dữ liệu sang file orders.xlsx
         return Excel::download(new OrderTiktokExport, 'order_tiktok.xlsx');
     }

    public function importOrders(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new OrderTiktokImport, $request->file('file'));

        return back()->with('success', 'Orders imported successfully.');
    }
}
