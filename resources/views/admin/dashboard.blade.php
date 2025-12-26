@extends('layout')
@section('title', 'Trang Quản Trị Hệ Thống')

@section('main')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Trang Quản Trị</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Thẻ Thống kê tổng quan -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Tổng Người Dùng</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ number_format($totalUsers ?? 1250) }}</h4>
                            <p class="text-muted mb-0 mt-2">
                                @php
                                    // Tính % thay đổi so với tháng trước
                                    $currentUsers = $totalUsers ?? 1250;
                                    $previousUsers = $previousMonthUsers ?? 1163;
                                    
                                    // Chỉ tính % tăng nếu có dữ liệu tháng trước
                                    if ($previousUsers > 0) {
                                        // Công thức đúng: (current - previous) / previous * 100
                                        $userChange = $currentUsers - $previousUsers;
                                        $userPercentChange = ($userChange / $previousUsers) * 100;
                                        
                                        $userPercentChangeFormatted = number_format(abs($userPercentChange), 1);
                                        $userChangeClass = $userChange >= 0 ? 'text-success' : 'text-danger';
                                        $userChangeIcon = $userChange >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
                                        $userChangePrefix = $userChange >= 0 ? '+' : '-';
                                    } else {
                                        $userPercentChangeFormatted = '0.0';
                                        $userChangeClass = 'text-muted';
                                        $userChangeIcon = 'ri-subtract-line';
                                        $userChangePrefix = '';
                                    }
                                @endphp
                                <span class="badge bg-light {{ $userChangeClass }} mb-0">
                                    <i class="{{ $userChangeIcon }} align-middle"></i> {{ $userChangePrefix }}{{ $userPercentChangeFormatted }}%
                                </span> so với tháng trước
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-3">
                                <i class="ri-user-3-line text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Đơn Hàng Tháng Này</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ number_format($monthlyOrders ?? 485) }}</h4>
                            <p class="text-muted mb-0 mt-2">
                                @php
                                    // Tính % thay đổi so với tháng trước
                                    $currentOrders = $monthlyOrders ?? 485;
                                    $previousOrders = $previousMonthOrders ?? 501;
                                    
                                    // Chỉ tính % tăng nếu có dữ liệu tháng trước
                                    if ($previousOrders > 0) {
                                        // Công thức đúng: (current - previous) / previous * 100
                                        $orderChange = $currentOrders - $previousOrders;
                                        $orderPercentChange = ($orderChange / $previousOrders) * 100;
                                        
                                        $orderPercentChangeFormatted = number_format(abs($orderPercentChange), 1);
                                        $orderChangeClass = $orderChange >= 0 ? 'text-success' : 'text-danger';
                                        $orderChangeIcon = $orderChange >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
                                        $orderChangePrefix = $orderChange >= 0 ? '+' : '-';
                                    } else {
                                        $orderPercentChangeFormatted = '0.0';
                                        $orderChangeClass = 'text-muted';
                                        $orderChangeIcon = 'ri-subtract-line';
                                        $orderChangePrefix = '';
                                    }
                                @endphp
                                <span class="badge bg-light {{ $orderChangeClass }} mb-0">
                                    <i class="{{ $orderChangeIcon }} align-middle"></i> {{ $orderChangePrefix }}{{ $orderPercentChangeFormatted }}%
                                </span> so với tháng trước
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle fs-3">
                                <i class="ri-shopping-bag-3-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Doanh Thu Tháng</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ number_format($monthlyRevenue ?? 121000000, 0, ',', '.') }} đ</h4>
                            <p class="text-muted mb-0 mt-2">
                                @php
                                    // Tính % thay đổi so với tháng trước
                                    $currentRevenue = $monthlyRevenue ?? 121000000;
                                    $previousRevenue = $previousMonthRevenue ?? 135000000; // Doanh thu tháng 5
                                    
                                    // Chỉ tính % tăng nếu có dữ liệu tháng trước
                                    if ($previousRevenue > 0) {
                                        // Công thức đúng: (current - previous) / previous * 100
                                        $revenueChange = $currentRevenue - $previousRevenue;
                                        $revenuePercentChange = ($revenueChange / $previousRevenue) * 100;
                                        
                                        // Kiểm tra kết quả tính toán
                                        // Ví dụ: 121 - 135 = -14, -14/135 * 100 = -10.37%
                                        
                                        $revenuePercentChangeFormatted = number_format(abs($revenuePercentChange), 1);
                                        $revenueChangeClass = $revenueChange >= 0 ? 'text-success' : 'text-danger';
                                        $revenueChangeIcon = $revenueChange >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
                                        $revenueChangePrefix = $revenueChange >= 0 ? '+' : '-';
                                    } else {
                                        $revenuePercentChangeFormatted = '0.0';
                                        $revenueChangeClass = 'text-muted';
                                        $revenueChangeIcon = 'ri-subtract-line';
                                        $revenueChangePrefix = '';
                                    }
                                    
                                    // Debug - hiển thị kết quả tính toán để kiểm tra
                                    // echo "<!-- Debug: Current=$currentRevenue, Previous=$previousRevenue, Change=$revenueChange, Percent=$revenuePercentChange, Formatted=$revenuePercentChangeFormatted -->";
                                @endphp
                                <span class="badge bg-light {{ $revenueChangeClass }} mb-0">
                                    <i class="{{ $revenueChangeIcon }} align-middle"></i> {{ $revenueChangePrefix }}{{ $revenuePercentChangeFormatted }}%
                                </span> so với tháng trước
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded-circle fs-3">
                                <i class="ri-money-dollar-circle-line text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Số Shop Hoạt Động</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ number_format($activeShops ?? 324) }}</h4>
                            <p class="text-muted mb-0 mt-2">
                                @php
                                    // Tính % thay đổi so với tháng trước
                                    $currentShops = $activeShops ?? 324;
                                    $previousShops = $previousMonthShops ?? 307;
                                    
                                    // Chỉ tính % tăng nếu có dữ liệu tháng trước
                                    if ($previousShops > 0) {
                                        // Công thức đúng: (current - previous) / previous * 100
                                        $shopChange = $currentShops - $previousShops;
                                        $shopPercentChange = ($shopChange / $previousShops) * 100;
                                        
                                        $shopPercentChangeFormatted = number_format(abs($shopPercentChange), 1);
                                        $shopChangeClass = $shopChange >= 0 ? 'text-success' : 'text-danger';
                                        $shopChangeIcon = $shopChange >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
                                        $shopChangePrefix = $shopChange >= 0 ? '+' : '-';
                                    } else {
                                        $shopPercentChangeFormatted = '0.0';
                                        $shopChangeClass = 'text-muted';
                                        $shopChangeIcon = 'ri-subtract-line';
                                        $shopChangePrefix = '';
                                    }
                                @endphp
                                <span class="badge bg-light {{ $shopChangeClass }} mb-0">
                                    <i class="{{ $shopChangeIcon }} align-middle"></i> {{ $shopChangePrefix }}{{ $shopPercentChangeFormatted }}%
                                </span> so với tháng trước
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-danger rounded-circle fs-3">
                                <i class="ri-store-2-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thống Kê Doanh Thu</h4>
                </div>
                <div class="card-body">
                    <div id="revenue-chart" class="apex-charts" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Phân Bổ Đơn Hàng</h4>
                </div>
                <div class="card-body">
                    <div id="order-distribution-chart" class="apex-charts" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chức năng quản trị -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Chức năng quản lý hệ thống</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-group fs-3 text-primary"></i>
                                    <h5 class="mt-3">Quản lý người dùng</h5>
                                    <p class="text-muted">Quản lý tài khoản và phân quyền</p>
                                    @if(Route::has('admin.users.index'))
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-store fs-3 text-success"></i>
                                    <h5 class="mt-3">Quản lý Shop</h5>
                                    <p class="text-muted">Quản lý thông tin và hoạt động</p>
                                    @if(Route::has('admin.shops.index'))
                                        <a href="{{ route('admin.shops.index') }}" class="btn btn-success btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-success btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-currency-usd fs-3 text-warning"></i>
                                    <h5 class="mt-3">Quản lý Giao dịch</h5>
                                    <p class="text-muted">Theo dõi và quản lý giao dịch</p>
                                    @if(Route::has('admin.transactions.index'))
                                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-warning btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-cog-outline fs-3 text-danger"></i>
                                    <h5 class="mt-3">Cấu hình hệ thống</h5>
                                    <p class="text-muted">Thiết lập các thông số hệ thống</p>
                                    @if(Route::has('admin.settings'))
                                        <a href="{{ route('admin.settings') }}" class="btn btn-danger btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-package-variant fs-3 text-info"></i>
                                    <h5 class="mt-3">Quản lý sản phẩm</h5>
                                    <p class="text-muted">Quản lý danh mục sản phẩm</p>
                                    @if(Route::has('admin.products.index'))
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-info btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-info btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-text-box-check-outline fs-3 text-secondary"></i>
                                    <h5 class="mt-3">Báo cáo & Thống kê</h5>
                                    <p class="text-muted">Xem báo cáo doanh số, doanh thu</p>
                                    @if(Route::has('admin.reports'))
                                        <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-bell-outline fs-3 text-primary"></i>
                                    <h5 class="mt-3">Quản lý thông báo</h5>
                                    <p class="text-muted">Gửi và quản lý thông báo hệ thống</p>
                                    @if(Route::has('admin.notifications'))
                                        <a href="{{ route('admin.notifications') }}" class="btn btn-primary btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-shield-account fs-3 text-dark"></i>
                                    <h5 class="mt-3">Kiểm tra an toàn</h5>
                                    <p class="text-muted">Kiểm tra và quản lý bảo mật</p>
                                    @if(Route::has('admin.security'))
                                        <a href="{{ route('admin.security') }}" class="btn btn-dark btn-sm">Truy cập</a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">Truy cập</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hoạt động gần đây -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Hoạt động gần đây</h5>
                        @if(Route::has('admin.activities'))
                            <a href="{{ route('admin.activities') }}" class="btn btn-primary btn-sm">Xem tất cả</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Xem tất cả</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Người dùng</th>
                                    <th scope="col">Hoạt động</th>
                                    <th scope="col">Thời gian</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#ACT-001</td>
                                    <td>Nguyễn Văn A</td>
                                    <td>Đăng ký tài khoản mới</td>
                                    <td>10:43 14/05/2023</td>
                                    <td><span class="badge bg-success">Hoàn thành</span></td>
                                    <td><button class="btn btn-sm btn-soft-info">Xem chi tiết</button></td>
                                </tr>
                                <tr>
                                    <td>#ACT-002</td>
                                    <td>Trần Thị B</td>
                                    <td>Nạp tiền vào tài khoản</td>
                                    <td>09:12 14/05/2023</td>
                                    <td><span class="badge bg-warning">Đang xử lý</span></td>
                                    <td><button class="btn btn-sm btn-soft-info">Xem chi tiết</button></td>
                                </tr>
                                <tr>
                                    <td>#ACT-003</td>
                                    <td>Lê Văn C</td>
                                    <td>Tạo shop mới</td>
                                    <td>08:45 14/05/2023</td>
                                    <td><span class="badge bg-success">Hoàn thành</span></td>
                                    <td><button class="btn btn-sm btn-soft-info">Xem chi tiết</button></td>
                                </tr>
                                <tr>
                                    <td>#ACT-004</td>
                                    <td>Phạm Thị D</td>
                                    <td>Yêu cầu rút tiền</td>
                                    <td>15:30 13/05/2023</td>
                                    <td><span class="badge bg-danger">Từ chối</span></td>
                                    <td><button class="btn btn-sm btn-soft-info">Xem chi tiết</button></td>
                                </tr>
                                <tr>
                                    <td>#ACT-005</td>
                                    <td>Hoàng Văn E</td>
                                    <td>Đăng sản phẩm mới</td>
                                    <td>14:25 13/05/2023</td>
                                    <td><span class="badge bg-success">Hoàn thành</span></td>
                                    <td><button class="btn btn-sm btn-soft-info">Xem chi tiết</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm ApexCharts JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Biểu đồ doanh thu
        var revenueOptions = {
            series: [
                {
                    name: "Doanh thu",
                    data: [
                        @foreach($revenueData as $month => $data)
                            {{ $data['revenue'] }},
                        @endforeach
                    ]
                },
                {
                    name: "Phí dropship",
                    data: [
                        @foreach($revenueData as $month => $data)
                            {{ $data['dropship_fee'] }},
                        @endforeach
                    ]
                }
            ],
            chart: {
                type: "area",
                height: 350,
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: "smooth",
                width: 2
            },
            colors: ["#4ade80", "#f87171"],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"]
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString('vi-VN') + " đ";
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toLocaleString('vi-VN') + " đ";
                    }
                }
            }
        };

        // Biểu đồ phân bổ đơn hàng với kiểm tra null
        var orderDistributionOptions = {
            series: [
                {{ $orderDistribution['Hoàn thành'] ?? 0 }},
                {{ $orderDistribution['Đang xử lý'] ?? 0 }},
                {{ $orderDistribution['Đang vận chuyển'] ?? 0 }},
                {{ $orderDistribution['Đã hủy'] ?? 0 }}
            ],
            chart: {
                type: "pie",
                height: 350
            },
            labels: ["Hoàn thành", "Đang xử lý", "Đang vận chuyển", "Đã hủy"],
            colors: ["#4ade80", "#facc15", "#60a5fa", "#f87171"],
            legend: {
                position: "bottom"
            },
            dataLabels: {
                enabled: false
            }
        };

        // Khởi tạo biểu đồ nếu các phần tử tồn tại
        var revenueChartElement = document.querySelector("#revenue-chart");
        var orderDistributionElement = document.querySelector("#order-distribution-chart");

        if (revenueChartElement) {
            var revenueChart = new ApexCharts(revenueChartElement, revenueOptions);
            revenueChart.render();
        }

        if (orderDistributionElement) {
            var orderDistributionChart = new ApexCharts(orderDistributionElement, orderDistributionOptions);
            orderDistributionChart.render();
        }
    });
</script>
@endsection
