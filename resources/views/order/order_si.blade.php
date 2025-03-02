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
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">

                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả đơn hàng
                                </a>
                            </li>
                            @foreach($shops as $shop)
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered" data-bs-toggle="tab" id="shop-{{$shop->id}}" href="#shop-{{$shop->id}}-content" role="tab" aria-selected="false">
                                    @if($shop->platform == 'Tiktok')
                                    <img src="https://res.cloudinary.com/dup7bxiei/image/upload/v1740887008/avatars/qiy9vtcrex1p4teq57lc.png" alt="" style="width: 20px; height: 20px;">
                                    @elseif($shop->platform == 'Shopee')
                                    <img src="https://res.cloudinary.com/dup7bxiei/image/upload/v1740886059/avatars/n3hv3omjxgvebrjey2rv.png" alt="" style="width: 20px; height: 20px;">
                                    @else
                                    <i class="fas fa-store me-1"></i>
                                    @endif
                                    {{$shop->shop_name}}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <!-- Tất cả đơn hàng -->
                            <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table id="orderTable" class="table table-hover">

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
                                            @foreach($orders as $item)
                                            <tr>
                                                <td class="id text-black-50" style="max-width: 5px;">
                                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                                        <li class="hienthicopy">
                                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$item->order_code}}">
                                                                {{$item->order_code}}
                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="text-body-secondary" style="font-size: 11px;">{{$item->filter_date}}</a>
                                                        </li>
                                                    </ul>
                                                </td>



                                                <td class="customer_cost" data-shop-id="{{ $order->shop->id ?? 0 }}">
                                                    @if($item->shop->platform == 'Tiktok')
                                                    <img src="https://res.cloudinary.com/dup7bxiei/image/upload/v1740887008/avatars/qiy9vtcrex1p4teq57lc.png" alt="" style="width: 20px; height: 20px;">
                                                    @elseif($item->shop->platform == 'Shopee')
                                                    <img src="https://res.cloudinary.com/dup7bxiei/image/upload/v1740886059/avatars/n3hv3omjxgvebrjey2rv.png" alt="" style="width: 20px; height: 20px;">
                                                    @endif
                                                    {{ $item->shop->shop_name ?? 'N/A' }}
                                                </td>
                                                <td class="export_date">{{$item->created_at}}</td>
                                                <td class="total_products">{{$item->total_products}}</td>
                                                <td class="total_dropship">{{ number_format($item->total_dropship, 0, ',', '.') }} đ</td>
                                                <td class="total_bill">{{ number_format($item->total_bill, 0, ',', '.') }} đ</td>
                                                @if($item->payment_status == 'Chưa thanh toán')
                                                <td class="payment_status" style="color:red;">
                                                    {{ $item->payment_status }}
                                                </td>
                                                @else
                                                <td class="payment_status" style="color:green;">
                                                    {{ $item->payment_status }}
                                                </td>
                                                @endif
                                                <td class="transaction_id">

                                                    <li style="list-style: none; padding: 0; margin: 0;" class="hienthicopy">
                                                        <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$item->transaction_id}}">
                                                            {{$item->transaction_id}}
                                                            <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                        </a>
                                                    </li>
                                                </td>
                                                <td class="reconciled">
                                                    @if($item->reconciled == 1)
                                                    Chưa đối soát
                                                    @elseif($item->reconciled == 0)
                                                    Đã đối soát
                                                    @endif
                                                </td>

                                                <td>
                                                    <ul class="list-inline hstack gap-2 mb-0 d-flex justify-content-center">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                            <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#staticBackdrop-{{$item->id}}">
                                                                <i class="ri-eye-fill fs-16"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="staticBackdrop-{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                                                                                @foreach($item->orderDetails as $detail)
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
                                                                                            <h6 class="fw-medium order-link text-dark" data-order-code="{{$item->order_code}}">
                                                                                                {{$item->order_code}}
                                                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                                                                <i class="d-flex text-dark ">{{$item->export_date}}</i>
                                                                                                <span class="badge badge-gradient-danger">{{ $item->shop->shop_name ?? 'N/A' }}</span>
                                                                                            </h6>
                                                                                            <td>Tổng số sản phẩm :</td>
                                                                                            <td class="text-end">{{ $item->total_products}} </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng phí Dropship :</td>
                                                                                            <td class="text-end">{{ number_format($item->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>Tổng tiền nhập hàng :</td>
                                                                                            <td class="text-end">{{ number_format($item->total_bill-$item->total_dropship, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        <tr class="border-top border-top-dashed">
                                                                                            <th scope="row">Tổng tiền đơn hàng (đ) :</th>
                                                                                            <th class="text-end">{{ number_format($item->total_bill, 0, ',', '.') }} đ</th>
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
                            <!-- Đơn hàng theo từng shop -->
                            @foreach($shops as $shop)
                            <div class="tab-pane fade" id="shop-{{$shop->id}}-content" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle table-hover" id="orderTableSHOP{{$shop->id}}">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th class="sort" data-sort="id">Mã đơn nhập hàng</th>
                                                <th class="sort" data-sort="shop_name">Shop</th>
                                                <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                                <th class="sort" data-sort="soluong">Số lượng</th>
                                                <th class="sort" data-sort="phidrop">Phí drop</th>
                                                <th class="sort" data-sort="product_cost">Tổng Bill</th>
                                                <th class="sort" data-sort="shop_name">Thanh toán</th>
                                                <th class="sort" data-sort="shop_name">Mã thanh toán</th>
                                                <th class="sort" data-sort="hanhdong">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black-50">
                                            @foreach($orders->where('shop_id', $shop->shop_id) as $order)
                                            <tr>
                                                <td class="id text-black-50" style="max-width: 5px;">
                                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                                        <li class="hienthicopy">
                                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$item->order_code}}">
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
                                                    {{ $order->shop->shop_name ?? 'N/A' }}
                                                </td>
                                                <td class="date">{{$order->export_date}}</td>
                                                <td class="customer_cost">{{$order->total_products}}</td>
                                                <td class="product_name">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                                                <td class="product_code">{{ number_format($order->total_bill, 0, ',', '.') }} đ</td>
                                                <td class="date">{{$order->payment_status}}</td>
                                                <td class="transaction_id">
                                                    <li style="list-style: none; padding: 0; margin: 0;" class="hienthicopy">
                                                        <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$order->transaction_id}}">
                                                            {{$order->transaction_id}}
                                                            <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                        </a>
                                                    </li>
                                                </td>
                                                <td>
                                                    <!-- Button trigger modal -->
                                                    <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal{{$order->order_code}}">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                            <i class="ri-eye-fill fs-16 text-primary"></i>
                                                        </li>
                                                    </a>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal{{$order->order_code}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" style="max-width: 70%; width: 100%;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body" style="display: flex; gap: 20px; overflow-x: auto; max-height: 800px;">
                                                                    <div class="col-xl-9" style="flex: 0 0 70%;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <div class="table-responsive table-card">
                                                                                    <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                                                                                        <table class="table table-nowrap align-middle table-borderless mb-0 table-hover ">
                                                                                            <thead class="table-light text-muted">
                                                                                                <tr>
                                                                                                    <th scope="col">Sản Phẩm</th>
                                                                                                    <th scope="col">Số Lượng</th>
                                                                                                    <th scope="col">Giá Nhập</th>
                                                                                                    <th scope="col" class="text-end">Tổng Giá Nhập</th>
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
                                                                                                    <td>{{$detail->quantity}}</td>
                                                                                                    <td>{{ number_format($detail->unit_cost, 0, ',', '.') }} đ</td>
                                                                                                    <td class="text-end">{{ number_format($detail->total_cost, 0, ',', '.') }} đ</td>
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
                                                                    <div class="col-xl-4" style="flex: 0 0 25%; position: sticky; top: 0;">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <table class="table table-borderless mb-0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <h6 class="modal-title" id="staticBackdropLabel">ID Đơn Hàng: {{ $order->order_code }}
                                                                                                <h7><i class="d-flex">{{$order->export_date}}</i></h7> <span class="badge badge-gradient-danger">{{ $order->shop->shop_name ?? 'N/A' }}</span>
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
                                    <script>
                                        $(document).ready(function() {
                                            $('#orderTableSHOP{{$shop->id}}').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                                                "order": [
                                                    [2, "desc"]
                                                ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                                                // Chỉnh Tiếng Việt
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ đơn hàng",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "🔍",
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
<!-- Include DataTables JS -->


<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId; // Gắn shopId vào dataset
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>
@endsection