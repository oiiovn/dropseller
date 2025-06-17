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

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .search-order {
        width: 100%;
        padding: 8px;
        margin-bottom: 1rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }

    .page-item:first-child .page-link {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }

    .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }

    .page-item.active .page-link {
        z-index: 1;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .highlight {
        background-color: yellow;
        font-weight: bold;
    }

    .page-wrapper {
        position: relative;
        min-height: 100vh;
    }

    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1001;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .sticky-tabs {
        position: sticky;
        top: 50px; /* Height of sticky header */
        background: white;
        z-index: 1000;
        border-bottom: 1px solid #dee2e6;
    }

    .content-area {
        position: relative;
        padding-bottom: 60px; /* Height of pagination controls */
    }

    .table-wrapper {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .pagination-controls {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 15px 20px;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination {
        margin: 0;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
    }

    .page-link {
        padding: 0.375rem 0.75rem;
        min-width: 38px;
        text-align: center;
    }

    /* Ensure modal appears above pagination controls */
    .modal {
        z-index: 10000;
    }

    .sticky-tabs {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1000;
        padding-top: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .content-wrapper {
        padding-bottom: 100px; /* Extra space for pagination */
    }

    .table-responsive {
        height: calc(100vh - 250px);
        overflow-y: auto;
    }
</style>

<div class="page-wrapper">
    <div class="container-fluid" style="width: 100%; background: white;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="orderList">
                    <!-- Sticky header with limit selector -->
                    <div class="sticky-header">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="me-2">Hiển thị:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="changeLimit(this.value)">
                                @foreach([10, 25, 50, 100] as $limit)
                                    <option value="{{ $limit }}" {{ $currentLimit == $limit ? 'selected' : '' }}>
                                        {{ $limit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Sticky tabs -->
                    <div class="sticky-tabs">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-0">
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
                    </div>

                    <!-- Main content area -->
                    <div class="content-area">
                        <div class="table-wrapper">
                            <!-- Content with margin -->
                            <div class="content-wrapper">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="all-orders">
                                        <div class="mb-3">
                                            <input type="text" class="search-order" id="searchAllOrders" placeholder="Tìm kiếm đơn hàng...">
                                        </div>

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
                                                    @foreach($pagedOrders as $order)
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
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @foreach($orders_all as $userName => $shops)
                                    <div class="tab-pane fade" id="user-{{ Str::slug($userName)}}">
                                        <div class="mb-3">
                                            <input type="text" class="search-order" id="search-{{ Str::slug($userName)}}" placeholder="Tìm kiếm trong {{ $userName }}...">
                                        </div>
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
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Fixed pagination at bottom -->
                            <div class="pagination-controls">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">Hiển thị:</span>
                                    <select class="form-select form-select-sm" style="width: auto; min-width: 80px" onchange="changeLimit(this.value)">
                                        @foreach([10, 25, 50, 100] as $limit)
                                            <option value="{{ $limit }}" {{ $currentLimit == $limit ? 'selected' : '' }}>
                                                {{ $limit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $pagedOrders->appends(['limit' => $currentLimit])->onEachSide(1)->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Search function
function filterTable(tableId, searchValue) {
    const value = searchValue.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    
    let hasResults = false;
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const show = text.includes(value);
        row.style.display = show ? '' : 'none';
        if (show) hasResults = true;
    });
    
    // Hide pagination if searching
    const paginationElement = document.querySelector('.pagination');
    if (paginationElement) {
        paginationElement.style.display = value ? 'none' : 'flex';
    }
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Main table search
    const mainSearch = document.getElementById('searchAllOrders');
    if (mainSearch) {
        mainSearch.addEventListener('input', throttle(function() {
            filterTable('orderddd', this.value);
        }, 200));
    }

    // User specific table searches
    @foreach($orders_all as $userName => $shops)
        const userSearch{{ Str::slug($userName) }} = document.getElementById('search-{{ Str::slug($userName) }}');
        if (userSearch{{ Str::slug($userName) }}) {
            userSearch{{ Str::slug($userName) }}.addEventListener('input', throttle(function() {
                filterTable('orderTable-{{ Str::slug($userName) }}', this.value);
            }, 200));
        }
    @endforeach

    // Add lazy loading to all images
    document.querySelectorAll('img').forEach(img => {
        img.loading = 'lazy';
    });
});

function initializeTable(tableId, itemsPerPage = 10) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    let currentPage = 1;
    
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    function fuzzyMatch(text, search) {
        search = search.toLowerCase();
        text = text.toLowerCase();
        const searchLen = search.length;
        if (searchLen === 0) return true;
        
        let searchIdx = 0;
        for (let i = 0; i < text.length && searchIdx < searchLen; i++) {
            if (text[i] === search[searchIdx]) {
                searchIdx++;
            }
        }
        return searchIdx === searchLen;
    }

    function filterAndPaginateRows(searchTerm = '') {
        const filteredRows = rows.filter(row => {
            const text = row.textContent.toLowerCase();
            return fuzzyMatch(text, searchTerm);
        });

        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const paginatedRows = filteredRows.slice(start, start + itemsPerPage);

        // Clear existing rows
        tbody.innerHTML = '';

        // Add filtered and paginated rows
        paginatedRows.forEach(row => {
            if (searchTerm) {
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    const originalText = cell.textContent;
                    if (fuzzyMatch(originalText, searchTerm)) {
                        cell.innerHTML = highlightText(originalText, searchTerm);
                    }
                });
            }
            tbody.appendChild(row.cloneNode(true));
        });

        // Update pagination
        updatePagination(totalPages);

        return filteredRows.length;
    }

    function updatePagination(totalPages) {
        const pagination = document.getElementById(`pagination-${tableId}`);
        pagination.innerHTML = '';

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                filterAndPaginateRows(document.getElementById('searchAllOrders').value);
            }
        });
        pagination.appendChild(prevButton);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.classList.toggle('active', i === currentPage);
            button.addEventListener('click', () => {
                currentPage = i;
                filterAndPaginateRows(document.getElementById('searchAllOrders').value);
            });
            pagination.appendChild(button);
        }

        // Next button
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                filterAndPaginateRows(document.getElementById('searchAllOrders').value);
            }
        });
        pagination.appendChild(nextButton);
    }

    // Initial setup
    filterAndPaginateRows();

    // Return the filter function for reuse
    return filterAndPaginateRows;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tables with search functionality
    const filterMainTable = initializeTable('orderddd', 10);

    // Add search event listener
    const searchInput = document.getElementById('searchAllOrders');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value;
        
        searchTimeout = setTimeout(() => {
            filterMainTable(searchTerm);
        }, 200); // Debounce for better performance
    });

    // Initialize all user-specific tables
    @foreach($orders_all as $userName => $shops)
        initializeTable('orderTable-{{ Str::slug($userName)}}', 10);
    @endforeach
});
</script>

<script>
function changeLimit(limit) {
    // Update URL with new limit
    let url = window.location.href;
    url = updateQueryStringParameter(url, 'limit', limit);
    
    // Reload the page with new limit
    window.location.href = url;
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}
</script>
@endsection