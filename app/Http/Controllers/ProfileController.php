<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Order;
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

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
    public function Get_all()
    {
        $users = User::all();
        foreach ($users as $user) {
            $shops = Shop::where('user_id', $user->id)->get();
            foreach ($shops as $shop) {
                $orders_unpaiddd = Order::where('shop_id', $shop->shop_id)
                    ->where('payment_status', 'Chưa thanh toán')
                    ->where('created_at', '<', Carbon::now()->subDay()) 
                    ->get();
                    $shop->orders_unpaid_count = $orders_unpaiddd->count();
                $shop->orders_unpaid = $orders_unpaiddd;
            }
            $user->shops = $shops;
        }

        return view('user.profie_user', compact('users'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
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
