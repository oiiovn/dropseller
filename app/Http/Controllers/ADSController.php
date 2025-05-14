<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ADS;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class ADSController extends Controller
{
    public function ADS()
    {

        $shops = Shop::all();
        return view('ads.add_ads', compact('shops'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required',
            'shop_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'amount' => 'required',
            'vat' => 'required',
            'total' => 'required',
        ]);
        $shop = Shop::where('shop_id', $validated['shop_id'])->get();
        $ad = new ADS();
        $ad->invoice_id = $validated['invoice_id'];
        $ad->shop_id = $validated['shop_id'];
        $ad->date_range = $validated['start_date'] . ' - ' . $validated['end_date'];
        $ad->amount = $validated['amount'];
        $ad->vat = $validated['vat'];
        $ad->total_amount = $validated['total'];
        $ad->payment_status = 'Chưa thanh toán';
        $ad->payment_code = null;
        $ad->save();
        foreach ($shop as $shop) {
            Notification::create([
                'user_id' => $shop->user_id,
                'shop_id' => $validated['shop_id'],
                'image' => '  https://res.cloudinary.com/dup7bxiei/image/upload/v1739331584/5d6b33d2d4816adf3390_iwkcee.jpg',
                'title' => 'Quảng cáo mới cần thanh toán',
                'message' => 'Mã ' . $validated['invoice_id'] . ' quảng cáo mới cần thanh toán',
            ]);
        }
        return redirect()->back()->with('success', 'Thêm quảng cáo thành công');
    }
    public function ads_all()
    {
        // Lấy tất cả Users có Shops, trong Shops có Quảng Cáo
        $users = User::with('shops.ads')->get();
        $ads_all = [];

        foreach ($users as $user) {
            $userName = $user->name ?? 'Unknown User';

            if (!isset($ads_all[$userName])) {
                $ads_all[$userName] = [];
            }

            foreach ($user->shops as $shop) {
                $shopName = $shop->shop_name ?? 'Unknown Shop';

                if (!isset($ads_all[$userName][$shopName])) {
                    $ads_all[$userName][$shopName] = [];
                }

                if ($shop->ads->isNotEmpty()) {
                    foreach ($shop->ads as $ads) {
                        $ads_all[$userName][$shopName][] = [
                            'invoice_id' => $ads->invoice_id,
                            'date_range' => $ads->date_range,
                            'shop_id' => $ads->shop_id,
                            'amount' => $ads->amount,
                            'vat' => $ads->vat,
                            'total_amount' => $ads->total_amount,
                            'payment_status' => $ads->payment_status,
                            'payment_code' => $ads->payment_code ?? 'Chưa có mã thanh toán',
                            'created_at' => $ads->created_at,
                        ];
                    }
                }
            }
        }

        return view('ads.ads_all', compact('ads_all'));
    }

    public function ads_shop()
    {
        // Lấy user đang đăng nhập
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem quảng cáo.');
        }
        $shops = $user->shops()->with('ads')->get();
        $ads_shop = [];

        foreach ($shops as $shop) {
            $shopName = $shop->shop_name ?? 'Unknown Shop';

            if (!isset($ads_shop[$shopName])) {
                $ads_shop[$shopName] = [];
            }

            if ($shop->ads->isNotEmpty()) {
                foreach ($shop->ads as $ads) {
                    $ads_shop[$shopName][] = [
                        'invoice_id' => $ads->invoice_id,
                        'date_range' => $ads->date_range,
                        'shop_id' => $ads->shop_id,
                        'amount' => $ads->amount,
                        'vat' => $ads->vat,
                        'total_amount' => $ads->total_amount,
                        'payment_status' => $ads->payment_status,
                        'payment_code' => $ads->payment_code ?? 'Chưa có mã thanh toán',
                        'created_at' => $ads->created_at,
                    ];
                }
            }
        }
        return view('ads.ads_shop', compact('ads_shop', 'user'));
    }
}
