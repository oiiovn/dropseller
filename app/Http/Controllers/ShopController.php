<?php

namespace App\Http\Controllers;

use App\Imports\ShopsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
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
    public function shops()
    {
        $shops = Shop::all();
        $users = User::all();
        return view('shops.shops_info',compact('shops','users'));
    }
    public function create()
{
    return view('shops.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'shop_id' => 'required|max:255',
        'shop_name' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id',
    ]);

    // Debug dữ liệu được gửi
   

    // Kiểm tra shop_id đã tồn tại
    $exists = Shop::where('shop_id', $validated['shop_id'])->exists();
    if ($exists) {
        return redirect()->back()->with('error', 'Shop ID đã tồn tại. Vui lòng nhập ID khác.');
    }
    // dd($exists);

    Shop::create($validated);
    return redirect()->back()->with('success', 'Thêm shop thành công!');
}



public function update(Request $request, Shop $shop)
{
    $validator = Validator::make($request->all(), [
        'shop_id' => 'required|max:255|unique:shops_name,shop_id,' . $shop->id,
        'shop_name' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id',
    ]);
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    $shop->update($request->only(['shop_id', 'shop_name', 'user_id']));

    return redirect()->back()->with('success', 'Cập nhật shop thành công!');
}

public function destroy(Shop $shop)
{
    try {
        $shop->delete();
        return redirect()->back()->with('success', 'Xóa shop thành công!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Xóa shop thất bại! Vui lòng thử lại.');
    }
}

    

    
}
