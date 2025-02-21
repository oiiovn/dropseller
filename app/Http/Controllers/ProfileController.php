<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Order;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function viewProfile()
    {
        $user = Auth::user();
        $shops = Shop::where('user_id', $user->id)->get();

        foreach ($shops as $shop) {
            $shop->revenue = Order::where('shop_id', $shop->shop_id)->sum('total_bill');
            $orders = Order::where('shop_id', $shop->shop_id)->get();
        }
        return view('profile.profile', compact('user', 'shops'));
    }

    // Hàm cập nhật hồ sơ người dùng
    public function updateProfile(Request $request)
    {
        // Lấy user hiện tại
        $user = Auth::user();

        // Kiểm tra dữ liệu nhập vào
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed', // Nhập lại mật khẩu
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Giới hạn 2MB
        ]);

        // Nếu có lỗi validation, trả về lỗi
        if ($validator->fails()) {
            dd($validator->errors()); // In lỗi ra màn hình
            return redirect()->back()->withErrors($validator)->withInput();
        }
        

        // Cập nhật thông tin nếu có
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $uploadedImage = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'avatars' 
            ]);
            $imageUrl = $uploadedImage->getSecurePath();
            $user->image = $imageUrl;
        }
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
