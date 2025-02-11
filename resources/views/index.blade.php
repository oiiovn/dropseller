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
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> + 16.24 %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>


                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>
                                                @if(isset($totalBillPaid_all) && $totalBillPaid_all > 0)
                                                {{ number_format($totalBillPaid_all, 0) ?? 0 }}
                                                @elseif(isset($totalBillPaid) && $totalBillPaid > 0)
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
                                    <div class="flex-shrink-0">
                                        <h5 class="text-danger fs-14 mb-0">
                                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i> - 3.57 %
                                        </h5>
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
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> + 29.08 %
                                        </h5>
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
                                    <div class="flex-shrink-0">
                                        <h5 class="text-muted fs-14 mb-0">
                                            + 0.00 %
                                        </h5>
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
                                <h4 class="card-title mb-0 flex-grow-1">Top sản phẩm</h4>
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
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0 " style="height: 400px">
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                                            <img src="{{ $product->image }}" alt="" class="img-fluid d-block" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1">
                                                                <a href="apps-ecommerce-product-details.html" class="text-reset">
                                                                    {{ $product->product_name }}
                                                                </a>
                                                            </h5>
                                                            <span class="text-muted">{{ $product->sku }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">Giá</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ number_format($product->unit_cost, 0) }} VNĐ</h5>
                                                </td>
                                                <td>
                                                    <span class="text-muted">Đơn hàng</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ $product->order_count }}</h5>
                                                </td>
                                                <td>
                                                    <span class="text-muted">Lượt bán</span>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ $product->total_quantity }}</h5>
                                                </td>
                                                <td>
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
                                            Hiển thị <span class="fw-semibold">5</span> trên tổng <span class="fw-semibold">15</span> Sản phẩm
                                        </div>
                                    </div>
                                    <div class="col-sm-auto  mt-3 mt-sm-0">
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
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card card-height-100">
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
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0" style="height: 400px">
                                        <tbody>
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

                                                            " alt="" class="avatar-sm p-2" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a href="apps-ecommerce-seller-details.html" class="text-reset">{{$shop->shop->user->name ?? 'Vô Danh'}}</a>
                                                            </h5>
                                                            <span class="text-muted">{{$shop->shop->shop_name}}</span>
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

                                        </tbody>
                                    </table><!-- end table -->
                                </div>

                                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            Hiển thị <span class="fw-semibold">5</span> / <span class="fw-semibold">125</span> nhà bán
                                        </div>
                                    </div>
                                    <div class="col-sm-auto  mt-3 mt-sm-0">
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
                                    </div>
                                </div>

                            </div> <!-- .card-body-->
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->


            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>


</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".dash-filter-picker", {
            mode: "range", // Chế độ chọn khoảng ngày
            dateFormat: "d M, Y", // Định dạng ngày
            defaultDate: ["01 Jan, 2022", "31 Jan, 2022"], // Giá trị mặc định
            locale: "vn", // Hiển thị tiếng Việt
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    let startDate = selectedDates[0].toLocaleDateString("vi-VN"); // Ngày bắt đầu
                    let endDate = selectedDates[1].toLocaleDateString("vi-VN"); // Ngày kết thúc
                    console.log("Từ ngày:", startDate, "Đến ngày:", endDate);
                }
            }
        });

        // Xử lý khi form submit
        document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault();
            let dateRange = document.querySelector("input[name='date_range']").value;

            if (dateRange.includes(" to ")) {
                let [startDate, endDate] = dateRange.split(" to ");
                console.log("Từ ngày:", startDate, "Đến ngày:", endDate);
            } else {
                console.log("Vui lòng chọn khoảng ngày hợp lệ!");
            }
        });
    });
</script>



@endsection