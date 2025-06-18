@extends('layout')
@section('title', 'main')

@section('main')


<style>
    .hienthicopy .icon {
        display: none;
        cursor: pointer;
    }

    .hienthicopy:hover .icon {
        display: inline;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 2;
    }

    .search-box .clear-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        display: none;
    }

    .search-box input:valid~.clear-icon {
        display: inline;
    }
</style>
<div class="container-fluid" style=" width: 100%; background: white; ">
    <div class="row">
        <div class="col-lg-12">
            @if(Auth::check() && DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', Auth::id())
                ->where('roles.slug', 'admin')
                ->exists())
                <div class="alert alert-info">
                    Chào mừng Admin! Bạn có thể quản lý tất cả các đơn hàng ở đây.
                </div>
            @endif
            
            @if(Auth::check() && DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', Auth::id())
                ->where('roles.slug', 'manager')
                ->exists())
                <div class="alert alert-info">
                    Chào mừng Manager! Bạn có thể quản lý đơn hàng ở đây.
                </div>
            @endif
            
            <div class="card" id="orderList">
                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#all-orders" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả đơn hàng
                                </a>
                            </li>
                            @foreach($orders_all as $userName => $shops)
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered cursor-pointer" href="#user-{{ Str::slug($userName) }}" data-bs-toggle="tab" role="tab" aria-selected="false" style="cursor: pointer;">
                                    {{ $userName }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="all-orders">
                                <div class="table-responsive table-card mb-1">
                                    <table id="orderddd" class="table table-hover">
                                        <thead class="text-muted table-light ">
                                            <tr class="text-uppercase ">
                                                <th class="sort" data-sort="id">Mã đơn nhập hàng</th>
                                                <th class="sort" data-sort="shop_name">Shop</th>
                                                <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                                <th class="sort" data-sort="soluong">Số lượng</th>
                                                <th class="sort" data-sort="phidrop">Phí drop</th>
                                                <th class="sort" data-sort="product_cost">Tổng Bill</th>
                                                <th class="sort" data-sort="shop_name">Thanh toán</th>
                                                <th class="sort" data-sort="shop_name">Mã thanh toán</th>
                                                <th class="sort" data-sort="shop_name">Đối soát</th>
                                                <th class="sort" data-sort="hanhdong">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($orders_all as $shops)
                                            @foreach($shops as $shopName => $orders)
                                            @foreach($orders as $order)
                                            <tr>
                                                <td class="id text-black-50" style="max-width: 5px;">
                                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                                        <li class="hienthicopy">
                                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$order->order_code}}">
                                                                {{$order->order_code}}
                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="text-body-secondary" style="font-size: 11px;">{{$order->filter_date}}</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td class="customer_cost" data-shop-id="{{ $order->shop->id ?? 0 }}">
                                                    @if($order->shop->platform == 'Tiktok')
                                                    <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                    @elseif($order->shop->platform == 'Shoppe')
                                                    <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                    @endif
                                                    {{ $order->shop->shop_name ?? 'N/A' }}
                                                </td>
                                                <td class="export_date">{{$order->created_at}}</td>
                                                <td class="total_products">{{$order->total_products}}</td>
                                                <td class="total_dropship">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                                                <td class="total_bill">{{ number_format($order->total_bill, 0, ',', '.') }} đ</td>
                                                @if($order->payment_status == 'Chưa thanh toán')
                                                <td class="payment_status" style="color:red;">
                                                    {{ $order->payment_status }}
                                                </td>
                                                @else
                                                <td class="payment_status" style="color:green;">
                                                    {{ $order->payment_status }}
                                                </td>
                                                @endif
                                                <td class="transaction_id">

                                                    <li style="list-style: none; padding: 0; margin: 0;" class="hienthicopy">
                                                        <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$order->transaction_id}}">
                                                            {{$order->transaction_id}}
                                                            <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                        </a>
                                                    </li>
                                                </td>
                                                <td class="reconciled">
                                                    @if($order->reconciled == 1)
                                                    Chưa đối soát
                                                    @elseif($order->reconciled == 0)
                                                    Đã đối soát
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="list-inline hstack gap-2 mb-0 d-flex justify-content-center">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                            <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#staticBackdrop-{{$order->id}}">
                                                                <i class="ri-eye-fill fs-16"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="staticBackdrop-{{$order->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                        <div class="modal-dialog" style="max-width: 90%; width: 100%;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title" id="staticBackdropLabel"></h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <!-- Phần chi tiết sản phẩm -->
                                                                <div class="modal-body" style="display: flex; gap: 20px; overflow-x: auto; max-height: 1000px;">
                                                                    <div class="col-xl-9" style="flex: 0 0 67%;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <div class="table-responsive table-card">
                                                                                    <div class="table-responsive" style="max-height: 1000px; overflow-y: auto;">
                                                                                        <table class="table table-nowrap align-middle table-borderless mb-0 table-hover ">
                                                                                            <thead class="table-light text-muted">
                                                                                                <tr>
                                                                                                    <th scope="col" style="width: 50%;">Sản Phẩm</th>
                                                                                                    <th scope="col" style="width: 12%;">Số Lượng</th>
                                                                                                    <th scope="col" style="width: 15%;">Giá Nhập</th>
                                                                                                    <th scope="col" style="width: 20%;">Tổng Giá Nhập</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach($order->orderDetails as $detail)
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="d-flex">
                                                                                                            <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                                                                                <img src="{{$detail->image}}" alt="" class="img-fluid d-block">
                                                                                                            </div>
                                                                                                            <div class="flex-grow-1 ms-3">
                                                                                                                <h5 class="fs-13">
                                                                                                                    <a>{{$detail->product_name}}</a>
                                                                                                                </h5>
                                                                                                                <p class="text-muted mb-0 fs-11">SKU: <span class="fw-medium">{{$detail->sku}}</span></p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="text-center">{{$detail->quantity}}</td>
                                                                                                    <td class="text-center">{{ number_format($detail->unit_cost, 0, ',', '.') }} đ</td>
                                                                                                    <td class="text-center">{{ number_format($detail->total_cost, 0, ',', '.') }} đ</td>
                                                                                                </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Phần thanh toán tổng -->
                                                                    <div class="col-xl-4" style="flex: 0 0 27%; position: sticky; top: 0;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <table class="table table-borderless mb-0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <h6 class="fw-medium order-link text-dark" data-order-code="{{$order->order_code}}">
                                                                                                {{$order->order_code}}
                                                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                                                                <i class="d-flex text-dark ">{{$order->filter_date}}</i>
                                                                                                <span class="badge badge-gradient-danger">{{ $order->shop->shop_name ?? 'N/A' }}</span>
                                                                                            </h6>
                                                                                            <td>Tổng số sản phẩm :</td>
                                                                                            <td class="text-end">{{ $order->total_products}} </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng phí Dropship :</td>
                                                                                            <td class="text-end">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng tiền nhập hàng :</td>
                                                                                            <td class="text-end">{{ number_format($order->total_bill-$order->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr class="border-top border-top-dashed">
                                                                                            <th scope="row">Tổng tiền đơn hàng (đ) :</th>
                                                                                            <th class="text-end">{{ number_format($order->total_bill, 0, ',', '.') }} đ</th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            $('#orderddd').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                                "order": [
                                                    [2, "desc"]
                                                ], // Sắp xếp theo cột thứ 3 (Ngày đơn hàng) theo ngày mới nhất (desc)
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ đơn hàng",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "",
                                                    "paginate": {
                                                        "first": "Trang đầu",
                                                        "last": "Trang cuối",
                                                        "next": "Tiếp theo",
                                                        "previous": "Quay lại"
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                            @foreach($orders_all as $userName => $shops)
                            <div class="tab-pane fade" id="user-{{ Str::slug($userName)}}">
                                <div class="table-responsive table-card mb-1">
                                    <table id="orderTable-{{ Str::slug($userName)}}" class="table table-hover">
                                        <thead class="text-muted table-light ">
                                            <tr class="text-uppercase">
                                                <th class="sort" data-sort="id" style="width: 15%;">Mã đơn nhập hàng</th>
                                                <th class="sort" data-sort="shop_name" style="width: 10%;">Shop</th>
                                                <th class="sort" data-sort="date" style="width: 13%;">Ngày tạo đơn</th>
                                                <th class="sort" data-sort="soluong" style="width: 7%;">Số lượng</th>
                                                <th class="sort" data-sort="phidrop" style="width: 10%;">Phí Drop</th>
                                                <th class="sort" data-sort="product_cost" style="width: 10%;">Tổng Bill</th>
                                                <th class="sort" data-sort="shop_name" style="width: 10%;">Thanh toán</th>
                                                <th class="sort" data-sort="shop_name" style="width:5%;">Mã thanh toán</th>
                                                <th class="sort" data-sort="shop_name" style="width: 8%;">Đối soát</th>
                                                <th class="sort" data-sort="hanhdong" style="width: 5%;">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($shops as $shopName => $orders)
                                            @foreach($orders as $order)
                                            <tr>
                                                <td class="id text-black-50" style="width: 15%;">
                                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                                        <li class="hienthicopy">
                                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$order->order_code}}">
                                                                {{$order->order_code}}
                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="text-body-secondary" style="font-size: 11px;">{{$order->filter_date}}</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td class="customer_cost" data-shop-id="{{ $order->shop->id ?? 0 }}">
                                                    @if($order->shop->platform == 'Tiktok')
                                                    <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                    @elseif($order->shop->platform == 'Shoppe')
                                                    <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                    @endif
                                                    {{ $order->shop->shop_name ?? 'N/A' }}
                                                </td>
                                                <td class="export_date">{{$order->created_at}}</td>
                                                <td class="total_products">{{$order->total_products}}</td>
                                                <td class="total_dropship">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                                                <td class="total_bill">{{ number_format($order->total_bill, 0, ',', '.') }} đ</td>
                                                @if($order->payment_status == 'Chưa thanh toán')
                                                <td class="payment_status" style="color:red;">
                                                    {{ $order->payment_status }}
                                                </td>
                                                @else
                                                <td class="payment_status" style="color:green;">
                                                    {{ $order->payment_status }}
                                                </td>
                                                @endif
                                                <td class="transaction_id">

                                                    <li style="list-style: none; padding: 0; margin: 0;" class="hienthicopy">
                                                        <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$order->transaction_id}}">
                                                            {{$order->transaction_id}}
                                                            <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                        </a>
                                                    </li>
                                                </td>
                                                <td class="reconciled">
                                                    @if($order->reconciled == 1)
                                                    Chưa đối soát
                                                    @elseif($order->reconciled == 0)
                                                    Đã đối soát
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="list-inline hstack gap-2 mb-0 d-flex justify-content-center">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                            <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#staticBackdrop22-{{$order->id}}">
                                                                <i class="ri-eye-fill fs-16"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="staticBackdrop22-{{$order->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel22" aria-hidden="true">
                                                        <div class="modal-dialog" style="max-width: 90%; width: 100%;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title" id="staticBackdropLabel22"></h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <!-- Phần chi tiết sản phẩm -->
                                                                <div class="modal-body" style="display: flex; gap: 20px; overflow-x: auto; max-height: 1000px;">
                                                                    <div class="col-xl-9" style="flex: 0 0 67%;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <div class="table-responsive table-card">
                                                                                    <div class="table-responsive" style="max-height: 1000px; overflow-y: auto;">
                                                                                        <table class="table table-nowrap align-middle table-borderless mb-0 table-hover ">
                                                                                            <thead class="table-light text-muted">
                                                                                                <tr>
                                                                                                    <th scope="col" style="width: 50%;">Sản Phẩm</th>
                                                                                                    <th scope="col" style="width: 12%;">Số Lượng</th>
                                                                                                    <th scope="col" style="width: 15%;">Giá Nhập</th>
                                                                                                    <th scope="col" style="width: 20%;">Tổng Giá Nhập</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach($order->orderDetails as $detail)
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="d-flex">
                                                                                                            <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                                                                                <img src="{{$detail->image}}" alt="" class="img-fluid d-block">
                                                                                                            </div>
                                                                                                            <div class="flex-grow-1 ms-3">
                                                                                                                <h5 class="fs-13">
                                                                                                                    <a>{{$detail->product_name}}</a>
                                                                                                                </h5>
                                                                                                                <p class="text-muted mb-0 fs-11">SKU: <span class="fw-medium">{{$detail->sku}}</span></p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="text-center">{{$detail->quantity}}</td>
                                                                                                    <td class="text-center">{{ number_format($detail->unit_cost, 0, ',', '.') }} đ</td>
                                                                                                    <td class="text-center">{{ number_format($detail->total_cost, 0, ',', '.') }} đ</td>
                                                                                                </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Phần thanh toán tổng -->
                                                                    <div class="col-xl-4" style="flex: 0 0 27%; position: sticky; top: 0;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <table class="table table-borderless mb-0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <h6 class="fw-medium order-link text-dark" data-order-code="{{$order->order_code}}">
                                                                                                {{$order->order_code}}
                                                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                                                                <i class="d-flex text-dark ">{{$order->filter_date}}</i>
                                                                                                <span class="badge badge-gradient-danger">{{ $order->shop->shop_name ?? 'N/A' }}</span>
                                                                                            </h6>
                                                                                            <td>Tổng số sản phẩm :</td>
                                                                                            <td class="text-end">{{ $order->total_products}} </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng phí Dropship :</td>
                                                                                            <td class="text-end">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng tiền nhập hàng :</td>
                                                                                            <td class="text-end">{{ number_format($order->total_bill-$order->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr class="border-top border-top-dashed">
                                                                                            <th scope="row">Tổng tiền đơn hàng (đ) :</th>
                                                                                            <th class="text-end">{{ number_format($order->total_bill, 0, ',', '.') }} đ</th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            $('#orderTable-{{ Str::slug($userName)}}').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                                "order": [
                                                    [2, "desc"]
                                                ], // Sắp xếp theo cột thứ 3 (Ngày đơn hàng) theo ngày mới nhất (desc)
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ đơn hàng",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "",
                                                    "paginate": {
                                                        "first": "Trang đầu",
                                                        "last": "Trang cuối",
                                                        "next": "Tiếp theo",
                                                        "previous": "Quay lại"
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId;
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>


@endsection