<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\{User, Transaction, ReturnOrder, UserMonthlyReport, Order, Shop};

class GenerateMonthlyReport extends Command
{
    protected $signature = 'report:generate-monthly';
    protected $description = 'Tạo báo cáo quyết toán hàng tháng cho tất cả người dùng';

    public function handle()
    {
        $month = Carbon::now()->subMonth()->format('Y-m');
        $startDate = Carbon::parse($month, 'Asia/Ho_Chi_Minh')->startOfMonth();
        $endDate = Carbon::parse($month, 'Asia/Ho_Chi_Minh')->endOfMonth();

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
        foreach ($gopTheoUser as $report) {
            $id_QT = $this->generateUniqueTransactionId();
            $user = User::find($report['user_id']);
            $userCode = $user->referral_code;
<<<<<<< HEAD
            $totalTopup = Transaction::where('description', 'LIKE', "%$userCode%")
=======
            $totalTopup = Transaction::whereRaw("description REGEXP '[[:<:]]{$userCode}[[:>:]]'")
>>>>>>> c10092977d5f599cce749af994c469c1a3a65ad6
                ->where('bank', 'MBB')
                ->where('type', 'IN')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            $shopIds = Shop::where('user_id', $user->id)->pluck('shop_id')->toArray();
            $code_transction = Order::whereRaw("STR_TO_DATE(SUBSTRING_INDEX(filter_date, ' - ', 1), '%Y-%m-%d') BETWEEN ? AND ?", [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
                ->whereIn('shop_id', $shopIds)
                ->pluck('transaction_id');
            $totalPaid = Transaction::whereIn('transaction_id', $code_transction)
                ->sum('amount');
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
            // dd($totalCanceled);            
            $ending_balance = $totalTopup - $totalPaid - $totalPaid_ads + $totalCanceled;
            $total_chi = $totalPaid - $totalCanceled - $report['tong_tien_user'] - $report['tong_tien_user_dropship'];

            UserMonthlyReport::updateOrCreate(
                [
                    'user_id' => $report['user_id'],
                    'month' => $month,
                ],
                [
                    'id_QT' => $id_QT,
                    'total_topup' => $totalTopup,
                    'total_paid' => $totalPaid,
                    'total_paid_ads' => $totalPaid_ads,
                    'total_canceled' => $totalCanceled,
                    'total_return' => $report['tong_tien_user'],
                    'total_chi' => $total_chi,
                    'ending_balance' => $ending_balance,
                    'shop_details' => $report['shops'],
                    'Drop_ships' => $report['tong_tien_user_dropship'],
                    'status_payment' =>  'Chưa thanh toán',
                ]
            );
        }

        $this->info("✅ Đã tạo quyết toán cho tháng $month");
    }
    private function generateUniqueTransactionId()
    {
        do {
            $id_QT = 'QT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (UserMonthlyReport::where('id_QT', $id_QT)->exists());
        return $id_QT;
    }
}
