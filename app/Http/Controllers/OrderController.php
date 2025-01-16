<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\OrderTiktokExport;
use App\Imports\OrderTiktokimport;
use App\Models\OrderDetail;
use App\Models\Order;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class OrderController extends Controller
{
    function Getorder()
    {
        return view('order.order');
    }

    public function order_si()
    {
        $orders = Order::with(['shop', 'orderDetails'])->get();
        return view('order.order_si', compact('orders'));
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




    public function order(Request $request)
    {
        $filteredProducts = json_decode($request->input('data'), true);
        $filterDate = $request->input('filterDate');
        $excludedCodes = ['QUA_TRANG', 'QUA001'];

        $totalAmount = 0;
        $totalRevenue = 0;

        // Tính tổng revenue và amount, bỏ qua các mã cần loại trừ
        foreach ($filteredProducts as $order) {
            $totalRevenue += $order['db_price'] * $order['amount'];
            if (!in_array($order['code'], $excludedCodes)) {
                $totalAmount += $order['amount'];
            }
        }
        // Danh sách shopId không tính phí
        $excludedShopIds = ['7495109251985279454', '7495962777620351819'];

        // Kiểm tra và tính toán phí
        $shopId = (string) $request->input('shop_id');
        // dd($shopId, $excludedShopIds);
        if (!in_array($shopId, $excludedShopIds, true)) {
            $total_dropship = $totalAmount * 5000;
        } else {
            $total_dropship = 0;
          
        }
        
        $total_tong = $totalRevenue + $total_dropship;
        $orderCode = 'DROP' . substr(str_shuffle('0123456789'), 0, 12);
        $totalAmounts = array_sum(array_column($filteredProducts, 'amount'));
        try {
            // Lưu đơn hàng vào bảng orders
            $order = Order::create([
                'order_code' => $orderCode,
                'export_date' => now(),
                'filter_date' => $filterDate,
                'shop_id' => $shopId,
                'total_products' => $totalAmount,
                'total_dropship' => $total_dropship,
                'total_bill' => $total_tong,
                'payment_status' => 'Chưa thanh toán',
                'payment_code' => 'null',
            ]);

            // Lưu chi tiết đơn hàng vào bảng order_details
            foreach ($filteredProducts as $detail) {
                OrderDetail::create([
                    'order_id' => $order->id, // Truy cập ID từ $order
                    'shop_id' => $order->shop_id, // Lấy shop_name từ bảng orders
                    'sku' => $detail['code'],
                    'image' => $detail['name'],
                    'product_name' => $detail['name'],
                    'quantity' => $detail['amount'],
                    'unit_cost' => $detail['db_price'],
                    'total_cost' => $detail['amount'] * $detail['db_price'],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể lưu hóa đơn. Vui lòng thử lại.',
                'error' => $e->getMessage()
            ], 500);
        }

        // Trả về view với thông báo thành công
        return view('product.report', compact('filteredProducts', 'filterDate', 'totalAmounts'))
            ->with('success', 'Dữ liệu đã được xử lý thành công!');
    }
}
