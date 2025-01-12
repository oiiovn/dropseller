<?php

namespace App\Http\Controllers;

use App\Imports\ShopsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shop;

class ShopController extends Controller
{
    /**
     * Phương thức xử lý import dữ liệu từ file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', 
        ]);
        try {
            Excel::import(new ShopsImport, $request->file('file'));
            return back()->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    public function shop_one()
    {
        return view('shops.shops');
    }
    

    
}
