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

class OrderController extends Controller
{
    protected $defaultPerPage = 10;
    protected $perPageOptions = [10, 25, 50, 100];

    function Getorder()
    {
        return view('payment.transaction_all');
    }
    public function Get_orders_all(Request $request)
    {
        $itemsPerPage = (int) $request->get('limit', $this->defaultPerPage);
        if (!in_array($itemsPerPage, $this->perPageOptions)) {
            $itemsPerPage = $this->defaultPerPage;
        }
        
        $shops = Shop::with(['orders' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        $orders_all = [];
        $allOrders = collect([]);
        
        foreach ($shops as $shop) {
            $userName = $shop->user->name ?? 'Unknown User';
            if (!isset($orders_all[$userName])) {
                $orders_all[$userName] = [];
            }
            $shopOrders = $shop->orders->sortByDesc('created_at');
            $orders_all[$userName][$shop->shop_name] = $shopOrders;
            $allOrders = $allOrders->concat($shopOrders);
        }

        $allOrders = $allOrders->sortByDesc('created_at');
        
        $pagedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $allOrders->forPage($request->get('page', 1), $itemsPerPage),
            $allOrders->count(),
            $itemsPerPage,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('order.orders_all', [
            'orders_all' => $orders_all,
            'pagedOrders' => $pagedOrders,
            'currentLimit' => $itemsPerPage,
            'perPageOptions' => $this->perPageOptions
        ]);
    }

    public function apiGetOrdersAll(Request $request)
    {
        $tab = $request->input('tab', 'all-orders'); // Tab hiện tại, mặc định là "all-orders"

        $query = Shop::with(['orders' => function ($q) use ($request) {
            if ($request->has('search') && !empty($request->search)) {
                $q->where('order_code', 'like', '%' . $request->search . '%');
            }
            if ($request->has('sort') && $request->has('direction')) {
                $q->orderBy($request->sort, $request->direction);
            } else {
                $q->orderBy('created_at', 'desc');
            }
        }]);

        if ($tab !== 'all-orders') {
            $query->whereHas('user', function ($q) use ($tab) {
                $q->where('name', $tab); // Lọc theo tên user (tab)
            });
        }

        $shops = $query->paginate(10); // Phân trang với 10 dòng mỗi trang

        return response()->json($shops); // Trả về dữ liệu JSON
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

    
}
