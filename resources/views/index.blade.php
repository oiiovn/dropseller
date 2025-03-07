@extends('layout')
@section('title', 'main')

@section('main')

<div class="container-fluid">

    <div class="row">
        <div class="col">

            <div class="h-100">

                <!--end row-->

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate" style="background: linear-gradient(to bottom,#58d19d, #ffffff, #ffffff);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> TỔNG DOANH SỐ</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>


                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>
                                                @if(isset($totalBillPaid) && $totalBillPaid > 0)
                                                {{ number_format($totalBillPaid, 0) ?? 0 }}
                                                @else
                                                0
                                                @endif
                                            </span>
                                            VNĐ
                                        </h4>
                                        <a href="#" class="text-decoration-underline"></a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate" style="background: linear-gradient(to bottom,#fccb38, #ffffff, #ffffff);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">ĐƠN HÀNG</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{number_format($totalOrders,0 ??0)}}</span> Đơn</h4>
                                        <a href="#" class="text-decoration-underline"></a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate" style="background: linear-gradient(to bottom,#78cbff, #ffffff, #ffffff);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">SẢN PHẨM BÁN RA</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{number_format($totalQuantitySold,0 ??0)}}</span> Sản phẩm </h4>
                                        <a href="#" class="text-decoration-underline"></a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-package text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate" style="background: linear-gradient(to bottom,#dfb0ff, #ffffff, #ffffff);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> PHÍ DROP</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"> <span>{{number_format($total_dropship,0 ?? 0)}}</span> VNĐ</h4>
                                        <a href="#" class="text-decoration-underline"></a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title rounded fs-3" style="background:#fae0ff">
                                            <i class="bx bx-wallet text-pastel" style="color: #dfb0ff;"></i>

                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->
                <div class="row">
                    <div class="col-xl-7">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Top sản phẩm toàn sàn</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-semibold text-uppercase fs-12">Xem theo :</span>
                                            <span class="text-muted">
                                                {{ request('date_range', '30 ngày trước') }} <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>

                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Hôm nay']) }}">Hôm nay</a>

                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDay()->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->subDay()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Hôm qua']) }}">Hôm qua</a>

                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => '7 ngày trước']) }}">7 ngày trước</a>

                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => '30 ngày trước']) }}">30 ngày trước</a>

                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s'), 'date_range' => 'Tháng trước']) }}">Tháng trước</a>

                                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfMonth()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Tháng này']) }}">Tháng này</a>
                                        </div>
                                    </div>


                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-card" style="height: 430px">
                                    <table class="table table-hover table-centered align-middle" style="height: 430px">
                                        <tbody>
                                            @if($Products->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <h5 class="fs-14 my-3">Không có đơn hàng nào trong khoảng thời gian này.</h5>
                                                </td>
                                            </tr>
                                            @else
                                            @foreach($Products as $product)
                                            <tr>
                                                <td style="width: 35%;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                                            <img src="{{ $product->image }}" alt="" class="img-fluid d-block" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1">
                                                                <a class="text-reset">
                                                                    {{ $product->product_name }}
                                                                </a>
                                                            </h5>
                                                            <span class="text-muted">{{ $product->sku }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 15%;">
                                                    <span class="text-muted">Giá</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ number_format($product->unit_cost, 0) }} VNĐ</h5>
                                                </td>
                                                <td style="width: 13%;">
                                                    <span class="text-muted">Đơn hàng</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ $product->order_count }}</h5>
                                                </td>
                                                <td style="width: 15%;">
                                                    <span class="text-muted">Lượt bán</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ $product->total_quantity }}</h5>
                                                </td>
                                                <td style="width: 20%;">
                                                    <span class="text-muted">Doanh số tổng</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ number_format($product->total_revenue, 0) }} VNĐ</h5>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>

                                    </table>
                                </div>

                                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            Hiển thị <span class="fw-semibold">5</span> Sản phẩm
                                        </div>
                                    </div>
                                    <div class="col-sm-auto  mt-3 mt-sm-0">
                                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                                            <div>
                                                {{ $Products->onEachSide(1)->links() }}
                                            </div>


                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card ">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Top nhà bán</h4>
                                <!-- <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Tháng này<i class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Tháng trước</a>
                                            <a class="dropdown-item" href="#">Tuần này</a>
                                            <a class="dropdown-item" href="#">Hôm nay</a>
                                        </div>
                                    </div>
                                </div> -->
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class=" table-card">
                                    <table class="table table-centered table-hover align-middle" style="height: 442px">
                                        <tbody>
                                            @if($totalOrdersByShop->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <h5 class="fs-14 my-3">Không có nhà bán nào trong khoảng thời gian này.</h5>
                                                </td>
                                            </tr>
                                            @else
                                            @foreach($totalOrdersByShop as $shop)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="
                                                                @if(isset($shop->shop->user->image) && !empty($shop->shop->user->image))
                                                                    {{ $shop->shop->user->image }}
                                                                @else
                                                                    assets/images/companies/img-1.png
                                                                @endif
                                                            " alt="" class="avatar-sm " style=" border-radius:10px" />
                                                        </div>

                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a class="text-reset">{{$shop->shop->user->name ?? 'Vô Danh'}}</a>
                                                            </h5>
                                                            <span>
                                                                @if($shop->shop->platform == 'Tiktok')
                                                                <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                                @elseif($shop->shop->platform == 'Shoppe')
                                                                <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                                @else
                                                                <i class="fas fa-store me-1"></i>
                                                                @endif
                                                                

                                                                {{ $shop->shop->shop_name ?? 'Vô Danh' }}

                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>

                                                    <span class="text-muted">Đơn hàng</span>
                                                    <p class="mb-0">{{$shop->order_count}}</p>
                                                </td>
                                                <td>
                                                    <span class="text-muted">Doanh thu</span>
                                                    <p class="mb-0">{{ number_format($shop->total_revenue, 0, ',', '.') }} VNĐ</p>
                                                </td>
                                                <!-- <td>
                                                    <h5 class="fs-14 mb-0">32%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                                </td> -->
                                            </tr><!-- end -->
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table><!-- end table -->
                                </div>

                                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            Hiển thị <span class="fw-semibold">5</span> nhà bán
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-auto  mt-3 mt-sm-0">
                                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                                            <li class="page-item disabled">
                                                <a href="#" class="page-link">←</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">1</a>
                                            </li>
                                            <li class="page-item active">
                                                <a href="#" class="page-link">2</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">3</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">→</a>
                                            </li>
                                        </ul>
                                    </div> -->
                                </div>

                            </div> <!-- .card-body-->
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->


            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>


</div>

@endsection