<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\ReturnOrder;
use App\Models\UserMonthlyReport;
use App\Models\User;
use App\Models\Shop;

class SettlementController extends Controller
{
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month, 'Asia/Ho_Chi_Minh')->startOfMonth();
        $endDate = Carbon::parse($month, 'Asia/Ho_Chi_Minh')->endOfMonth();
        // Tính đơn hoàn cho tất cả user có đơn trong tháng
       
            
        $donDropship = Order::whereRaw("
            STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') 
            BETWEEN ? AND ?", [
            $startDate->toDateString(),
            $endDate->toDateString()
        ])
            ->where('payment_status', 'Đã thanh toán') // nếu cần lọc giống ReturnOrder
            ->get()
            ->groupBy(function ($item) {
                return $item->shop->user->id . '_' . $item->shop_id;
            })
            ->map(function ($group) {
                return [
                    'user_id' => $group->first()->shop->user->id,
                    'shop_id' => $group->first()->shop_id,
                    'tong_tien_dropship' => $group->sum('total_dropship'),
                ];
            });
            $donHoan = ReturnOrder::with('shop.user')
            ->where('payment_status', 'Đã thanh toán')
            ->whereBetween('ngay', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->shop->user->id . '_' . $item->shop_id;
            })
            ->map(function ($group) {
                return [
                    'user_id' => $group->first()->shop->user->id,
                    'shop_id' => $group->first()->shop_id,
                    'tong_tien_hoan' => $group->sum('tong_tien'),
                ];
            })
            ->values();
            $donHoan = $donHoan->map(function ($item) use ($donDropship) {
                $key = $item['user_id'] . '_' . $item['shop_id'];
                $dropshipData = $donDropship->get($key);
                return array_merge($item, [
                    'tong_tien_dropship' => $dropshipData['tong_tien_dropship'] ?? 0,
                ]);
            });
            $gopTheoUser = collect($donHoan)
            ->groupBy('user_id')
            ->map(function ($items, $userId) {
                return [
                    'user_id' => $userId,
                    'shops' => $items->map(function ($item) {
                        return [
                            'shop_id' => $item['shop_id'],
                            'tong_tien_hoan' => $item['tong_tien_hoan'],
                            'tong_tien_dropship' => $item['tong_tien_dropship'],
                        ];
                    })->values(),
                    'tong_tien_user' => $items->sum('tong_tien_hoan'),
                    'tong_tien_user_dropship' => $items->sum('tong_tien_dropship'),
                ];
            })
            ->values();
        
        // Lưu từng bản ghi theo user vào bảng báo cáo
        foreach ($gopTheoUser as $report) {
            $IDQT = $this->generateUniqueTransactionId();
            $user = User::find($report['user_id']);
            $userCode = $user->referral_code;

            $totalTopup = Transaction::where('description', 'LIKE', "%$userCode%")
                ->where('bank', 'MBB')
                ->where('type', 'IN')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            // dd($report);
            $shopIds = Shop::where('user_id', $user->id)->pluck('shop_id')->toArray();
            $code_transction = Order::whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
                ->whereIn('shop_id', $shopIds)
                ->pluck('transaction_id');
            $totalPaid = Transaction::whereIn('transaction_id', $code_transction)
                ->sum('amount');
            // return response()->json($totalPaid);
            $totalPaid_ads = Transaction::where('account_number', $userCode)
                ->where('bank', 'ADS')
                ->where('type', 'OUT')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            $code_order = Order::whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
                ->whereIn('shop_id', $shopIds)
                ->pluck('order_code');
            $totalCanceled = Transaction::where(function ($query) use ($code_order) {
                foreach ($code_order as $code) {
                    $query->orWhere('description', 'LIKE', "%huỷ đơn $code%");
                }
            })
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->type === 'IN'
                        ? $transaction->amount
                        : -$transaction->amount;
                });



            $ending_balance = $totalTopup - $totalPaid - $totalPaid_ads + $totalCanceled;
            $total_chi = $totalPaid  - $totalCanceled - $report['tong_tien_user'];
            UserMonthlyReport::updateOrCreate(
                [
                    'user_id' => $report['user_id'],
                    'month' => $month,
                ],
                [
                    'id_QT' => $IDQT,
                    'total_topup' => $totalTopup,
                    'total_paid' => $totalPaid,
                    'total_paid_ads' => $totalPaid_ads,
                    'total_canceled' => $totalCanceled,
                    'total_return' => $report['tong_tien_user'],
                    'total_chi' => $total_chi,
                    'ending_balance' => $ending_balance,
                    'shop_details' => $report['shops'],
                    'Drop_ships' => $report['tong_tien_user_dropship'],
                ]
            );
            // Nếu là user hiện tại thì dùng cho view
            if (Auth::id() == $report['user_id']) {
                $currentUserData = compact(
                    'totalTopup',
                    'totalPaid',
                    'totalPaid_ads',
                    'totalCanceled',
                    'ending_balance',
                    'total_chi',
                );
            }
        }
        $data = array_merge([
            'month' => $month,
            'gopTheoUser' => $gopTheoUser,
            'totalTopup' => 0,
            'totalPaid' => 0,
            'totalPaid_ads' => 0,
            'totalCanceled' => 0,
            'ending_balance' => 0,
            'total_chi' => 0,
            'Drop_ships' => 0,
        ], $currentUserData ?? []);

        return view('settlement.settlement-detail', $data);
    }

    public function settlementReport()
    {

        $quyet_toan = UserMonthlyReport::where('user_id', Auth::id())
            ->where('tien_thuc_te', '!=', 0)
            ->orderBy('month', 'desc')
            ->get();
        $quyet_toan_thang_truoc = $quyet_toan->first(); // Lấy tháng gần nhất có tiền thực tế

        return view('settlement.monthly', compact('quyet_toan', 'quyet_toan_thang_truoc'));
    }
    private function generateUniqueTransactionId()
    {
        do {
            $ID_QT = 'QT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (UserMonthlyReport::where('id_QT', $ID_QT)->exists());
        return $ID_QT;
    }
}
