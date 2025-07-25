<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\BalanceHistory;
use Illuminate\Http\Request;

class BalanceHistoryController extends Controller
{
    public function index()
    {
        $query = BalanceHistory::where('user_id', Auth::id());

        // Tìm kiếm
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%")
                  ->orWhere('note', 'like', "%$search%")
                  ;
            });
        }

        // Lọc theo loại giao dịch
        if (request('type')) {
            $query->where('type', request('type'));
        }

        // Phân trang
        $histories = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        return view('balance.history', compact('histories'));
    }

    /**
     * API endpoint để lấy lịch sử số dư
     */
    public function apiIndex(Request $request)
    {
        $query = BalanceHistory::where('user_id', Auth::id());

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%")
                  ->orWhere('note', 'like', "%$search%");
            });
        }

        // Lọc theo loại giao dịch
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Phân trang
        $limit = $request->input('limit', 12);
        $page = $request->input('page', 1);
        
        $histories = $query->orderByDesc('created_at')
                          ->paginate($limit, ['*'], 'page', $page);

        // Format dữ liệu trả về
        $formattedHistories = $histories->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'transaction_code' => $item->transaction_code ?? '---',
                'type' => $item->type,
                'type_label' => $this->getTypeLabel($item->type),
                'amount_change' => $item->amount_change,
                'balance_after' => $item->balance_after,
                'note' => $item->note,
                'created_at' => $item->created_at->format('d/m/Y H:i'),
                'created_at_raw' => $item->created_at->toISOString(),
                'is_positive' => $item->amount_change >= 0,
                'formatted_amount' => ($item->amount_change >= 0 ? '+' : '-') . number_format(abs($item->amount_change)) . ' VND',
                'formatted_balance' => number_format($item->balance_after) . ' VND'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'histories' => $formattedHistories,
                'pagination' => [
                    'current_page' => $histories->currentPage(),
                    'last_page' => $histories->lastPage(),
                    'per_page' => $histories->perPage(),
                    'total' => $histories->total(),
                    'from' => $histories->firstItem(),
                    'to' => $histories->lastItem(),
                    'has_more_pages' => $histories->hasMorePages(),
                ],
                'filters' => [
                    'search' => $request->search,
                    'type' => $request->type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]
            ]
        ]);
    }

    /**
     * Lấy danh sách các loại giao dịch có sẵn
     */
    public function getTransactionTypes()
    {
        $types = BalanceHistory::where('user_id', Auth::id())
                              ->distinct()
                              ->pluck('type')
                              ->map(function($type) {
                                  return [
                                      'value' => $type,
                                      'label' => $this->getTypeLabel($type)
                                  ];
                              })
                              ->values();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics(Request $request)
    {
        $query = BalanceHistory::where('user_id', Auth::id());

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $statistics = [
            'total_transactions' => $query->count(),
            'total_deposits' => $query->clone()->where('type', 'deposit')->sum('amount_change'),
            'total_withdrawals' => abs($query->clone()->where('type', 'withdraw')->sum('amount_change')),
            'total_orders' => abs($query->clone()->where('type', 'order')->sum('amount_change')),
            'total_refunds' => $query->clone()->where('type', 'refund')->sum('amount_change'),
            'total_ads' => abs($query->clone()->where('type', 'ads')->sum('amount_change')),
            'total_product_fees' => abs($query->clone()->where('type', 'product_fee')->sum('amount_change')),
            'current_balance' => Auth::user()->total_amount,
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Chuyển đổi type thành label tiếng Việt
     */
    private function getTypeLabel($type)
    {
        return match($type) {
            'deposit' => 'Nạp tiền',
            'withdraw' => 'Quyết toán',
            'order' => 'Đơn hàng',
            'refund' => 'Hoàn huỷ',
            'ads' => 'Quảng cáo',
            'Monthly' => 'Quyết toán',
            'product_fee' => 'Phí đăng sản phẩm',
            default => ucfirst($type)
        };
    }
}

