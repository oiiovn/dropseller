<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\{User, Transaction, ReturnOrder, UserMonthlyReport};

class GenerateMonthlyReport extends Command
{
    protected $signature = 'report:generate-monthly';
    protected $description = 'Tạo báo cáo quyết toán hàng tháng cho tất cả người dùng';

    public function handle()
    {
        $month = Carbon::now()->subMonth()->format('Y-m');
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

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

        $gopTheoUser = collect($donHoan)
            ->groupBy('user_id')
            ->map(function ($items, $userId) {
                return [
                    'user_id' => $userId,
                    'shops' => $items->map(function ($item) {
                        return [
                            'shop_id' => $item['shop_id'],
                            'tong_tien_hoan' => $item['tong_tien_hoan'],
                        ];
                    })->values(),
                    'tong_tien_user' => $items->sum('tong_tien_hoan'),
                ];
            })
            ->values();

        foreach ($gopTheoUser as $report) {
            $id_QT = $this->generateUniqueTransactionId();
            $user = User::find($report['user_id']);
            $userCode = $user->referral_code;
            $totalTopup = Transaction::where('description', 'LIKE', "%$userCode%")
                ->where('bank', 'MBB')
                ->where('type', 'IN')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');

            $totalPaid = Transaction::where('account_number', $userCode)
                ->where('bank', 'DROP')
                ->where('type', 'OUT')
                ->whereBetween('transaction_date', [$startDate->copy()->addDays(), $endDate->copy()->addDays()])
                ->sum('amount');

            $totalPaid_ads = Transaction::where('account_number', $userCode)
                ->where('bank', 'ADS')
                ->where('type', 'OUT')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');

            $totalCanceled = Transaction::where('description', 'LIKE', "%$userCode%")
                ->where('bank', 'DROP')
                ->where('type', 'IN')
                ->whereBetween('transaction_date', [
                    $startDate->copy()->addDays(20),
                    $endDate->copy()->addDays(20)
                ])
                ->sum('amount');

            $ending_balance = $totalTopup - $totalPaid - $totalPaid_ads + $totalCanceled;
            $total_chi = $totalPaid - $totalCanceled - $report['tong_tien_user'];

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
