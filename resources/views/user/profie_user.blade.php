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
                                    <i class="ri-store-2-fill me-1 align-bottom"></i>Tất cả khách hàng
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Tất cả đơn hàng -->
                            <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table id="user_list" class="table table-hover">
                                        <thead class="text-muted table-light ">
                                            <tr class="text-uppercase ">
                                                <th class="sort" data-sort="soluong">Thông tin khách hàng</th>
                                                <th class="sort" data-sort="phidrop">Số Sư</th>
                                                <th class="sort" data-sort="product_cost">Đơn quá hạn thanh toán</th>
                                                <th class="sort" data-sort="date">Email</th>
                                                <th class="sort" data-sort="product_cost">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($users as $user)
                                            <tr>
                                                <td class="total_dropship" style="vertical-align: middle; width: 25%;">
                                                    <div class="position-relative d-flex align-items-center">

                                                        <!-- Thêm dòng chữ thông báo trên cùng -->


                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="
                                                                @if(isset($user->image) && !empty($user->image))
                                                                    {{ $user->image }}
                                                                @else
                                                                   https://img.icons8.com/ios-filled/100/user-male-circle.png
                                                                @endif
                                                            " alt="" class="avatar-sm" style="border-radius:10px" />
                                                        </div>

                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <b> <a class="text-reset">{{$user->name ?? 'Vô Danh'}}</a></b>
                                                            </h5>
                                                            <span>Code: <b style="color:#2e397f;">{{ $user->referral_code ?? 'CODE' }}</b></span>
                                                        </div>
                                                        @foreach ($user->shops as $shop)
                                                        @if($shop->orders_unpaid_count > 0)
                                                        <div class="h-100 d-flex align-items-center">
                                                            <span class="badge bg-danger m-4 py-2">
                                                                Chậm Thanh Toán
                                                            </span>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="total_products" style=" vertical-align: middle;">
                                                    {{ number_format($user->total_amount, 0, ',', '.') }} VNĐ
                                                </td>
                                                <td class="total_products" style=" vertical-align: middle;">
                                                    @foreach ($user->shops as $shop)
                                                    <p><b>
                                                            @if($shop->platform == 'Tiktok')
                                                            <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                            @elseif($shop->platform == 'Shoppe')
                                                            <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                            @else
                                                            <i class="fas fa-store me-1"></i>
                                                            @endif
                                                            {{ $shop->shop_name }}
                                                        </b>: <strong>{{ $shop->orders_unpaid_count ?? 0 }} Đơn</strong></p>
                                                    @endforeach
                                                </td>
                                                <td class="total_products" style=" vertical-align: middle;">{{$user->email}}</td>
                                                <td style="vertical-align: middle;">
                                                    <ul class="list-inline d-flex justify-content-center gap-2 mb-0">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                            <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#user-{{$user->id}}">
                                                                <i class="ri-eye-fill fs-16"></i>
                                                            </a>
                                                        </li>
                                                    </ul>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="user-{{$user->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTitle-{{$user->id}}" aria-hidden="true">
                                                        <div class="modal-dialog" style="max-width: 90%; width: 100%;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title" id="modalTitle-{{$user->id}}">Đơn hàng chậm thanh toán</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table table-hover">
                                                                        <thead class="text-muted table-light ">
                                                                            <tr class="text-uppercase ">
                                                                                <th class="sort" data-sort="id">Mã đơn nhập hàng</th>
                                                                                <th class="sort" data-sort="shop_name">Tên Chủ shop</th>
                                                                                <th class="sort" data-sort="shop_name">Shop</th>
                                                                                <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                                                                <th class="sort" data-sort="soluong">Số lượng</th>
                                                                                <th class="sort" data-sort="phidrop">Phí drop</th>
                                                                                <th class="sort" data-sort="product_cost">Tổng Bill</th>
                                                                                <th class="sort" data-sort="shop_name">Thanh toán</th>


                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="list form-check-all text-black-50">
                                                                            @foreach ($user->shops as $shop)
                                                                            @foreach($shop->orders_unpaid as $orders_unpai)
                                                                            <tr>
                                                                                <td class="id text-black-50" style="max-width: 5px;">
                                                                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                                                                        <li class="hienthicopy">
                                                                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$orders_unpai->order_code}}">
                                                                                                {{$orders_unpai['order_code']}}
                                                                                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a class="text-body-secondary" style="font-size: 11px;">{{$orders_unpai->filter_date}}</a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </td>
                                                                                <td class="customer_cost">
                                                                                    {{ $orders_unpai->shop->user->name?? 'N/A' }}
                                                                                </td>

                                                                                <td class="customer_cost" data-shop-id="{{ optional($orders_unpai->shop)->id ?? 0 }}">
                                                                                    @if($shop->platform == 'Tiktok')
                                                                                    <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                                                    @elseif($shop->platform == 'Shoppe')
                                                                                    <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                                                    @else
                                                                                    <i class="fas fa-store me-1"></i>
                                                                                    @endif

                                                                                    {{ optional($orders_unpai->shop)->shop_name ?? 'N/A' }}
                                                                                </td>


                                                                                <td class="export_date">{{$orders_unpai->created_at}}</td>
                                                                                <td class="total_products">{{$orders_unpai->total_products}}</td>
                                                                                <td class="total_dropship">{{ number_format($orders_unpai->total_dropship, 0, ',', '.') }} đ</td>
                                                                                <td class="total_bill">{{ number_format($orders_unpai->total_bill, 0, ',', '.') }} đ</td>
                                                                                @if($orders_unpai->payment_status == 'Chưa thanh toán')
                                                                                <td class="payment_status" style="color:red;">
                                                                                    {{ $orders_unpai->payment_status }}
                                                                                </td>
                                                                                @else
                                                                                <td class="payment_status" style="color:green;">
                                                                                    {{ $orders_unpai->payment_status }}
                                                                                </td>
                                                                                @endif
                                                                            </tr>
                                                                            @endforeach
                                                                            @endforeach
                                                                        </tbody>

                                                                    </table>

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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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