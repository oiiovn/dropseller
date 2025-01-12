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
   function Getorder(){
    return view('order.order');
   }
   
   function order_si(){
      return view('order.order_si');
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
        $totalRevenue += $order['revenue'];
        if (!in_array($order['code'], $excludedCodes)) {
            $totalAmount += $order['amount'];
        }
    }

    // Tính toán dropship và tổng bill
    $total_dropship = $totalAmount * 5000; 
    $total_tong = $totalRevenue + $total_dropship; 
    $orderCode = 'ORDER-' . Str::random(8);

    try {
        // Lưu đơn hàng vào bảng orders
        $order = Order::create([
            'order_code' => $orderCode,
            'export_date' => now(),
            'filter_date' => $filterDate,
            'shop_id' => 'Chưa có tên',
            'total_products' => $totalAmount,
            'total_dropship' => $total_dropship,
            'total_bill' => $total_tong,
            'payment_status' => 'pending',
            'payment_code' => 'null',
        ]);

        // Lưu chi tiết đơn hàng vào bảng order_details
        foreach ($filteredProducts as $detail) {
            OrderDetail::create([
                'order_id' => $order->id, // Truy cập ID từ $order
                'shop_id' => $order->shop_id, // Lấy shop_name từ bảng orders
                'sku' => $detail['code'], 
                'product_name' => $detail['name'], 
                'quantity' => $detail['amount'], 
                'unit_cost' => $detail['revenue'], 
                'total_cost' => $detail['revenue'], 
            ]);
        }

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Không thể lưu hóa đơn. Vui lòng thử lại.',
            'error' => $e->getMessage()
        ], 500);
    }

    // Trả về view với thông báo thành công
    return view('product.report', compact('filteredProducts', 'filterDate'))
        ->with('success', 'Dữ liệu đã được xử lý thành công!');
}

}
