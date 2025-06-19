<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\OrderTiktokExport;
use App\Imports\OrderTiktokimport;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date; // thêm ở đầu file
use App\Models\Product;
use App\Models\Transaction; // Import the Transaction model
use App\Models\ReturnOrder; // Import the ReturnOrder model
use Yajra\DataTables\Facades\DataTables;
class OrderController extends Controller
{
    public function getOrdersData(Request $request)
{
    // Start with a base query
    $query = Order::with('shop.user')->orderByDesc('created_at');
    
    // Filter by user if specified
    if ($request->has('user')) {
        $userSlug = $request->user;
        $userName = str_replace('-', ' ', $userSlug);
        
        $query = $query->whereHas('shop.user', function($q) use ($userName) {
            $q->where(DB::raw("LOWER(name)"), 'LIKE', "%" . strtolower($userName) . "%");
        });
    }
    
    return DataTables::of($query)
        ->addColumn('shop_name', function($order) {
            $platform = '';
            if ($order->shop && $order->shop->platform == 'Tiktok') {
                $platform = '<img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px; margin-right: 5px;">';
            } elseif ($order->shop && $order->shop->platform == 'Shoppe') {
                $platform = '<img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px; margin-right: 5px;">';
            }
            return $platform . ($order->shop ? $order->shop->shop_name : 'N/A');
        })
        ->addColumn('created_at', fn($order) => $order->created_at->format('d/m/Y H:i'))
        ->addColumn('filter_date', fn($order) => $order->filter_date)
        ->addColumn('total_products', fn($order) => $order->total_products)
        ->addColumn('total_dropship', fn($order) => number_format($order->total_dropship, 0, ',', '.') . ' đ')
        ->addColumn('total_bill', fn($order) => number_format($order->total_bill, 0, ',', '.') . ' đ')
        ->addColumn('payment_status', function($order) {
            $color = $order->payment_status == 'Chưa thanh toán' ? 'red' : 'green';
            return '<span style="color:'.$color.';">'.$order->payment_status.'</span>';
        })
        ->addColumn('transaction_id', fn($order) => $order->transaction_id ?: 'N/A')
        ->addColumn('reconciled', function($order) {
            return $order->reconciled ? '<span style="color:red;">Chưa đối soát</span>' : '<span style="color:green;">Đã đối soát</span>';
        })
        ->addColumn('action', fn($order) => '<button class="btn btn-sm btn-primary view-details" data-id="'.$order->id.'"><i class="ri-eye-line"></i></button>')
        ->rawColumns(['shop_name', 'payment_status', 'reconciled', 'action'])
        ->make(true);
}

    function Getorder()
    {
        return view('payment.transaction_all');
    }
    public function Get_orders_all()
    {
        $shops = Shop::with('orders')->get();
        $orders_all = [];

        foreach ($shops as $shop) {
            $userName = $shop->user->name ?? 'Unknown User'; 

            if (!isset($orders_all[$userName])) {
                $orders_all[$userName] = []; 
            }

            $orders_all[$userName][$shop->shop_name] = $shop->orders; 
        }

        return view('order.orders_all', compact('orders_all'));
    }



    public function order_si(Request $request)
    {
        $user = Auth::user();
        $shops = Shop::where('user_id', $user->id)->get();

        $ordersQuery = Order::whereIn('shop_id', $shops->pluck('shop_id'))
            ->with(['shop', 'orderDetails'])
            ->orderBy('created_at', 'desc');

        if ($request->has('order_code') && !empty($request->order_code)) {
            $ordersQuery->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        $orders = $ordersQuery->get();
        return view('order.order_si', compact('orders', 'shops'));
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
        $shopId = (string) $request->input('shop_id');

        $excludedCodes = ['QUA_TRANG', 'QUA001'];
        $excludedShopIds = ['7495109251985279454', '7495962777620351819'];
        $totalAmount = 0;
        $totalRevenue = 0;
        foreach ($filteredProducts as $order) {
            $totalRevenue += $order['db_price'] * $order['amount'];
            if (!in_array($order['code'], $excludedCodes)) {
                $totalAmount += $order['amount'];
            }
        }
        $total_dropship = in_array($shopId, $excludedShopIds, true) ? 0 : $totalAmount * 5000;
        $total_tong = $totalRevenue + $total_dropship;

        $orderCode = 'DROP' . substr(str_shuffle('0123456789'), 0, 12);
        $totalAmounts = array_sum(array_column($filteredProducts, 'amount'));
        $order = Order::where('filter_date', $filterDate)
            ->where('shop_id', $shopId)
            ->first();
        if ($order) {
            $isSame = (
                $order->total_products == $totalAmount &&
                $order->total_dropship == $total_dropship &&
                $order->total_bill == $total_tong
            );

            if ($isSame) {
                return redirect()->route('productsss')->with('error', 'Đơn hàng này đã có sẵn');
            } else {
                $order->update([
                    'total_products' => $totalAmount,
                    'total_dropship' => $total_dropship,
                    'total_bill' => $total_tong,
                ]);
                $existingSkus = collect($filteredProducts)->pluck('code')->toArray();
                OrderDetail::where('order_id', $order->id)
                    ->whereNotIn('sku', $existingSkus)
                    ->delete();
                foreach ($filteredProducts as $detail) {
                    $orderDetail = OrderDetail::where('order_id', $order->id)
                        ->where('sku', $detail['code'])
                        ->first();
                    if ($orderDetail) {
                        $orderDetail->update([
                            'shop_id' => $order->shop_id,
                            'sku' => $detail['code'],
                            'image' => $detail['image'],
                            'product_name' => $detail['name'],
                            'quantity' => $detail['amount'],
                            'unit_cost' => $detail['db_price'],
                            'total_cost' => $detail['amount'] * $detail['db_price'],
                        ]);
                    } else {
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'shop_id' => $order->shop_id,
                            'sku' => $detail['code'],
                            'image' => $detail['image'],
                            'product_name' => $detail['name'],
                            'quantity' => $detail['amount'],
                            'unit_cost' => $detail['db_price'],
                            'total_cost' => $detail['amount'] * $detail['db_price'],
                        ]);
                    }
                }

                return redirect()->route('productsss')->with('success', 'Đơn hàng đã được cập nhật thành công!');
            }
        } else {
            $order = Order::create([
                'order_code' => $orderCode,
                'export_date' => now(),
                'filter_date' => $filterDate,
                'shop_id' => $shopId,
                'total_products' => $totalAmount,
                'total_dropship' => $total_dropship,
                'total_bill' => $total_tong,
                'payment_status' => 'Chưa thanh toán',
                'payment_code' => null,
            ]);
            foreach ($filteredProducts as $detail) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'shop_id' => $order->shop_id,
                    'sku' => $detail['code'],
                    'image' => $detail['image'],
                    'product_name' => $detail['name'],
                    'quantity' => $detail['amount'],
                    'unit_cost' => $detail['db_price'],
                    'total_cost' => $detail['amount'] * $detail['db_price'],
                ]);
            }

            return redirect()->route('productsss')->with('success', 'Đơn hàng này đã được tạo mới!');
        }
    }
    // đơn hoàn
    public function showImportForm()
    {
        return view('order.import_don_hoan');
    }


    public function import(Request $request)
    {
        $rows = Excel::toArray([], $request->file('file'))[0];
        $data = collect($rows)->skip(1);
        $ketQua = [];
        foreach ($data as $row) {
            [$ngays, $sku, $so_luong, $shopId] = $row;
            $ngay = Date::excelToDateTimeObject($ngays)->format('Y-m-d');
            $donHangs = DB::table('orders')
                ->where('shop_id', $shopId)
                ->whereRaw("? BETWEEN SUBSTRING_INDEX(filter_date, ' - ', 1) AND SUBSTRING_INDEX(filter_date, ' - ', -1)", [$ngay])
                ->get();
            if ($donHangs->isEmpty()) {
                $ketQua[] = [
                    'ngay' => $ngay,
                    'shop_id' => $shopId,
                    'sku' => $sku,
                    'order_code' => null,
                    'filter_date' => null,
                    'ket_qua' => '❌ Không tìm thấy đơn nào'
                ];
            } else {
                foreach ($donHangs as $don) {
                    $product = Product::where('sku', $sku)
                        ->with(['order_detail' => function ($q) use ($don) {
                            $q->where('order_id', $don->id);
                        }])
                        ->first();
                    $ketQua[] = [
                        'ngay' => $ngay,
                        'shop_id' => $shopId,
                        'sku' => $sku,
                        'so_luong' => (int) $so_luong,
                        'order_code' => $don->order_code,
                        'filter_date' => $don->filter_date,
                        'gia_san_pham' => $product->price ?? 0,
                        'ket_qua' => '✅',
                        'product' => $product, // ⭐ thêm toàn bộ thông tin sản phẩm tại đây
                    ];
                }
            }
        }
        $shops = Shop::all();
        $timThay = collect($ketQua)->where('order_code', '!=', null);
        $khongTimThay = collect($ketQua)->where('order_code', null);

        $gopTimThay = $timThay
            ->groupBy(fn($item) => $item['ngay'] . '|' . $item['shop_id'] . '|' . $item['order_code'])
            ->map(function ($group) {
                $first = $group->first();
                $skuGrouped = $group->groupBy('sku')
                    ->map(function ($items) {
                        $tong = $items->sum('so_luong');
                        return $items->first()['sku'] . ' (' . $tong . ')';
                    })->values()->implode(', ');

                return [
                    'ngay' => $first['ngay'],
                    'shop_id' => $first['shop_id'],
                    'order_code' => $first['order_code'],
                    'filter_date' => $first['filter_date'],
                    'sku' => $skuGrouped,
                    'tong_tien' => $group->sum(fn($item) => $item['so_luong'] * $item['gia_san_pham']),
                    'ket_qua' => '✅ ',
                ];
            });

        $gopKhongTimThay = $khongTimThay
            ->groupBy(fn($item) => $item['ngay'] . '|' . $item['shop_id'])
            ->map(function ($group) {
                $first = $group->first();
                $skuList = $group->pluck('sku')->implode(', ');

                return [
                    'ngay' => $first['ngay'],
                    'shop_id' => $first['shop_id'],
                    'order_code' => null,
                    'filter_date' => null,
                    'sku' => $skuList,
                    'tong_tien' => 0,
                    'ket_qua' => '❌',
                ];
            });

        $ketQuaGop = $gopTimThay
            ->merge($gopKhongTimThay)
            ->sortByDesc(fn($item) => $item['ket_qua'] === '❌')
            ->values();

        $sanPhamGop = collect($ketQua)
            ->filter(fn($item) => !empty($item['order_code'])) // ✅ chỉ lấy dòng có đơn hàng
            ->groupBy('sku')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'sku' => $first['sku'],
                    'product_name' => $first['product']->order_detail[0]->product_name ?? '',
                    'image' => $first['product']->order_detail[0]->image ?? '',
                    'so_luong' => $items->sum('so_luong'),
                ];
            })
            ->values()
            ->sortBy('sku')
            ->values();
        $tongSanPham = $sanPhamGop->sum('so_luong');
        return view('order.import_don_hoan', ['ketQua' => $ketQuaGop, 'sanPhamGop' => $sanPhamGop, 'tongSanPham' => $tongSanPham , 'shops' => $shops]);
    }
    public function taoThanhToan(Request $request)
    {
        $ketQuaGop = collect(unserialize(base64_decode($request->input('data'))));

        $donCanThanhToan = $ketQuaGop->filter(function ($item) {
            return $item['order_code'] !== null && $item['tong_tien'] > 0;
        })->values();
// dd($donCanThanhToan);
        if ($donCanThanhToan->isNotEmpty()) {
            foreach ($donCanThanhToan as $don) {
                ReturnOrder::create([
                    'order_code' => $don['order_code'],
                    'shop_id' => $don['shop_id'],
                    'ngay' => $don['ngay'],
                    'sku' => json_encode($don['sku']), // có thể là chuỗi hoặc mảng → encode lại nếu là text
                    'tong_tien' => $don['tong_tien'],
                    'payment_status' => 'Chưa thanh toán',
                    'transaction_id' => null,
                ]);
            }

            return redirect()->back()->with('message', '✅ Đã tạo thanh toán thành công!');
        } else {
            return redirect()->back()->with('error', '❌ Không có đơn hợp lệ để thanh toán.');
        }
    }

    public function getOrderDetails($id)
{
    try {
        $order = Order::with(['shop', 'orderDetails'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'details' => $order->orderDetails,
            'shop' => $order->shop
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}