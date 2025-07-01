@extends('layout')
@section('title', 'main')

@section('main')
<style>
    .product-card {
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .product-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    .product-name {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* mặc định hiển thị 2 dòng */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-body h4 {
        font-size: 18px !important;
    }

    .card-body p {
        font-size: 15px !important;
    }

    @media (max-width: 768px) {
        .product-card {
            flex-direction: row;
            font-size: 12px;
        }

        .product-card img {
            width: 70px !important;
            height: 70px !important;
        }
    }

    @media (max-width: 576px) {
        .product-card .d-flex.justify-content-between {
            flex-direction: column;
            gap: 0.5rem;

        }

        .product-card .text-end {
            text-align: left !important;
        }

        .product-card {
            font-size: 10px;
        }

        .card-title {
            font-size: 12px;
        }

        .product-name {
            -webkit-line-clamp: 1;
        }

        .card-body h4 {
            font-size: 14px !important;
        }

        .card-body p,
        .card-body span,
        .card-body .text-muted {
            font-size: 12px !important;
        }

        .avatar-title.fs-3 {
            font-size: 18px !important;
            width: 36px;
            height: 36px;
            margin: 0 auto;
        }



    }


</style>
<div class="container-fluid">

    <div class="">
        <div class="">

            <div class="h-100">
            <div class="row g-3 mb-3">
                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card mb-0 card-animate">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0">TỔNG GIÁ VỐN</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 md:mt-3">
                                    <div>
                                        <h5 class="fw-semibold ff-secondary mb-1 fs-12 fs-md-16">
                                            <span class="d-inline-block text-nowrap">
                                                {{ number_format($totalBillPaid ?? 0) }} <span class="text-muted">VNĐ</span>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="md:avatar-sm avatar-xs flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card mb-0 card-animate">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 md:fs-12">ĐƠN HÀNG</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 md:mt-3">
                                    <div>
                                        <h5 class="fw-semibold ff-secondary mb-1 fs-12 md:fs-16">
                                            <span class="d-inline-block text-nowrap">
                                                {{ number_format($totalOrders ?? 0) }} <span class="text-muted">Đơn</span>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="md:avatar-sm avatar-xs flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card mb-0 card-animate">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 md:fs-12">SẢN PHẨM BÁN RA</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 md:mt-3">
                                    <div>
                                        <h5 class="fw-semibold ff-secondary mb-1 fs-12 md:fs-16">
                                            <span class="d-inline-block text-nowrap">
                                                {{ number_format($totalQuantitySold ?? 0) }} <span class="text-muted">Sản phẩm</span>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="md:avatar-sm avatar-xs flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-package text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card mb-0 card-animate">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 ">PHÍ DROP</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 md:mt-3">
                                    <div>
                                        <h5 class="fw-semibold ff-secondary mb-1 fs-12 md:fs-16">
                                            <span class="d-inline-block text-nowrap">
                                                {{ number_format($total_dropship ?? 0) }} <span class="text-muted">VNĐ</span>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="md:avatar-sm avatar-xs flex-shrink-0">
                                        <span class="avatar-title rounded fs-3" style="background:#fae0ff">
                                            <i class="bx bx-wallet" style="color: #dfb0ff;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Component -->
               
                
                
                <div class="row">
                    <div class="col-xl-7">
                        <div class="card h-100">
                            <div class="card-header align-items-center d-flex p-3">
                                <h4 class="card-title mb-0 flex-grow-1">Top sản phẩm toàn sàn</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-semibold text-uppercase fs-12">Xem theo:</span>
                                            <span class="text-muted">
                                                {{ request('date_range', 'Tháng này') }} <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Hôm nay']) }}">Hôm nay</a>

                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDay()->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->subDay()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Hôm qua']) }}">Hôm qua</a>

                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => '7 ngày trước']) }}">7 ngày trước</a>

                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => '30 ngày trước']) }}">30 ngày trước</a>

                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s'), 'date_range' => 'Tháng trước']) }}">Tháng trước</a>

                                            <a class="dropdown-item ajax-link" href="{{ request()->fullUrlWithQuery(['start_date' => now()->startOfMonth()->format('Y-m-d H:i:s'), 'end_date' => now()->endOfDay()->format('Y-m-d H:i:s'), 'date_range' => 'Tháng này']) }}">Tháng này</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-3">
                                {{-- Danh sách sản phẩm có scroll --}}
                                <div class="table-card table-responsive-custom mb-3">
                                    @if($Products->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <h5 class="fs-14 my-3">Không có đơn hàng nào trong khoảng thời gian này.</h5>
                                    </div>
                                    @else
                                    @foreach($Products as $product)
                                    <div class="product-card p-3 mb-2 border bg-white d-flex rounded">
                                        <div class="me-3">
                                            <img src="{{ $product->image }}" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start flex-nowrap">
                                                <div class="me-2">
                                                    <div class="text-muted small">Mã SP: {{ $product->sku }}</div>
                                                    <div class="fw-semibold mb-1 product-name">
                                                        {{ $product->product_name }}
                                                    </div>

                                                    <div class="text-muted small">
                                                        <span class="me-3">Đơn hàng: {{ $product->order_count }}</span>
                                                        <span>Lượt bán: {{ $product->total_quantity }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-end flex-shrink-0" style="min-width: 140px;">
                                                    <div class="fw-semibold text-primary">Giá vốn: {{ number_format($product->unit_cost) }} VNĐ</div>
                                                    <div class="small text-muted">Tổng giá vốn: <strong>{{ number_format($product->total_revenue) }} VNĐ</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>

                                {{-- Phân trang cố định bên dưới --}}
                                <div class="align-items-center pt-3 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            Hiển thị <span class="fw-semibold">{{ $Products->count() }}</span> Sản phẩm
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        <div class="d-flex justify-content-center justify-content-sm-end">
                                            {{ $Products->onEachSide(0)->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div> <!-- end .h-100-->
            </div> <!-- end col -->
        </div>
    </div>
    <!-- <div class="modal fade" id="autoModal" tabindex="-1" aria-labelledby="autoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="autoModalLabel">Thông báo hệ thống</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p>
                        🔴 <strong>Đơn huỷ sẽ được đối soát sau 3 ngày</strong> (trước đây là 19 ngày),
                        đảm bảo cho nhà bán được hoàn tiền đơn huỷ <strong>sớm nhất</strong>!
                    </p>

                    <hr>

                    <p>
                        🔄 Giá của sản phẩm sẽ được cập nhật từ <strong>websi.vn</strong>,
                        do đó có thể có sự <strong>chênh lệch nhỏ</strong> giữa các thời điểm.
                    </p>
                    <p class="text-danger fw-bold">📅 Chính sách này được áp dụng từ ngày 01/06.</p>

                    <hr>

                    <h6>📞 Thông tin hỗ trợ</h6>
                    <p>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ với quản trị viên hoặc đội ngũ kỹ thuật.</p>
                    <div class="text-center mt-3">
                        <img style="width:250px; height:300px;"
                            src="{{ asset('assets/images/IMG_1043.JPG') }}"
                            alt="Hỗ trợ kỹ thuật"
                            class="img-fluid rounded border shadow-sm">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tôi đã hiểu</button>
                </div>
            </div>
        </div>
    </div> -->


    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('autoModal'));
            modal.show();
        });
    </script>

    @if($showWelcomeModal)
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="welcomeModalLabel">🎉 Gói đăng sản phẩm mới!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p>👋 Xin chào,</p>
                    <p>Shop bạn đang có sản phẩm mới cần lên 🎯</p>
                    <p>Bạn nhấn đăng ngay để <strong>Đăng </strong> sản phẩm lên shop nhé.</p>
                    <div class="text-center mt-3">
                        <a href="{{ route('list_program') }}" class="btn btn-success waves-effect waves-light">
                            Đăng Ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($hasNegativeBalance)

    <!-- Modal cảnh báo số dư âm -->
    <div class="modal fade" id="negativeBalanceModal"
        tabindex="-1"
        aria-labelledby="negativeBalanceLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"> {{-- ✅ Không cho phép bấm ra ngoài và nhấn ESC để đóng --}}
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="negativeBalanceLabel">⚠ Cảnh báo số dư âm</h5>
                    {{-- ✅ Nút đóng bị ẩn khi số dư < 0 --}}
                    @if(Auth::user()->total_amount >= 0)
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    @endif
                </div>
                <div class="modal-body text-center">
                    Bạn vừa được quyết toán đơn tháng vừa rồi<br>
                    Vui lòng kiểm tra lại số dư của bạn.<br>
                    Nạp thêm tiền để được sử dụng các tính năng của hệ thống.<br>
                    Nếu đã nạp tiền vui lòng đợi 3-5 phút để hệ thống cập nhật bạn sẽ được sử dụng các tính năng của hệ thống.<br>
                    <br>
                    <span class="text-danger fw-bold">
                        Số dư hiện tại: {{ number_format(Auth::user()->total_amount, 0, ',', '.') }} VNĐ
                    </span>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="javascript:void(0);"
                        id="openNapTienModal"
                        data-amount="{{ Auth::user()->total_amount }}">
                        Nạp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let modalZIndex = 1050;
            let negativeBalanceModalShown = false;

            // Mỗi lần mở modal → tăng z-index
            $(document).on('show.bs.modal', '.modal', function() {
                const $modal = $(this);
                const $backdrop = $('.modal-backdrop').not('.stacked');

                modalZIndex += 20;
                $modal.css('z-index', modalZIndex);

                // Nếu có backdrop → cũng tăng z-index theo modal
                if ($backdrop.length) {
                    $backdrop.addClass('stacked').css('z-index', modalZIndex - 10);
                }

                // Nếu là modal cảnh báo số dư âm, đánh dấu là đã hiện
                if ($modal.attr('id') === 'negativeBalanceModal') {
                    negativeBalanceModalShown = true;
                }
            });

            // Khi đóng modal → hạ z-index và kiểm tra nếu cần hiển thị lại modal số dư âm
            $(document).on('hidden.bs.modal', '.modal', function() {
                modalZIndex -= 20;

                // Nếu modal đóng là modal nạp tiền, khôi phục modal cảnh báo số dư âm
                if ($(this).attr('id') === 'napTienModal') {
                    $('#negativeBalanceModal').removeClass('behind');
                    const hasNegativeBalance = {
                        {
                            Auth::user() - > total_amount < 0 ? 'true' : 'false'
                        }
                    };

                    if (hasNegativeBalance) {
                        setTimeout(() => {
                            const negativeModal = new bootstrap.Modal(document.getElementById('negativeBalanceModal'));
                            negativeModal.show();
                        }, 300);
                    }
                }
                // Nếu modal đóng KHÔNG phải là modal cảnh báo số dư âm
                // và modal cảnh báo số dư âm đã từng hiển thị trước đó
                // và số dư vẫn âm thì hiển thị lại modal cảnh báo
                else if ($(this).attr('id') !== 'negativeBalanceModal' && negativeBalanceModalShown) {
                    const hasNegativeBalance = {
                        {
                            Auth::user() - > total_amount < 0 ? 'true' : 'false'
                        }
                    };

                    if (hasNegativeBalance) {
                        setTimeout(() => {
                            const negativeModal = new bootstrap.Modal(document.getElementById('negativeBalanceModal'));
                            negativeModal.show();
                        }, 500); // Đợi modal hiện tại đóng hoàn toàn
                    }
                }
            });

            // Sự kiện mở modal nạp tiền - giữ negativeBalanceModal hiển thị ở phía sau
            $(document).on('click', '#openNapTienModal', function() {
                const amount = $(this).data('amount') || 0;

                // Đánh dấu modal hiện tại để giữ nó lại khi modal khác đóng
                if ($('#negativeBalanceModal').hasClass('show')) {
                    $('#negativeBalanceModal').addClass('behind');
                }

                $.get('{{ route("naptien") }}?amount=' + amount, function(data) {
                    if ($('#napTienModal').length === 0) {
                        $('body').append(data);
                    }

                    setTimeout(() => {
                        const modal = new bootstrap.Modal(document.getElementById('napTienModal'));
                        modal.show();
                    }, 50);
                });
            });
        });
    </script>

    <style>
        .modal.behind {
            z-index: 1040 !important;
            opacity: 0.5 !important;
            pointer-events: none;
            /* để modal dưới không chặn modal trên */
        }

        .modal-backdrop.show {
            z-index: 1039 !important;
        }

        .modal-backdrop.stacked {
            position: fixed !important;
        }
    </style>


    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showWelcome = {
                {
                    $showWelcomeModal ? 'true' : 'false'
                }
            };
            const showNegative = {
                {
                    $hasNegativeBalance ? 'true' : 'false'
                }
            };

            if (showWelcome) {
                const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();

                document.getElementById('welcomeModal').addEventListener('hidden.bs.modal', function() {
                    if (showNegative) {
                        const negativeModal = new bootstrap.Modal(document.getElementById('negativeBalanceModal'));
                        negativeModal.show();
                    }
                });
            } else if (showNegative) {
                const negativeModal = new bootstrap.Modal(document.getElementById('negativeBalanceModal'));
                negativeModal.show();
            }
        });
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Chart configuration
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // ========== LEGEND ICONS CONFIG ==========
            // Bạn có thể thay đổi icons tại đây:
            const LEGEND_ICONS = {
                totalBill: '',    // Icon cho "Tổng giá vốn" - có thể thay: 💵 💎 🪙 💳 
                feeDrop: '',      // Icon cho "Phí Drop" - có thể thay: 📦 🚛 ⚡ 🔄
                orders: '',       // Icon cho "Đơn hàng" - có thể thay: 🛒 📋 📝 🎯
                products: ''     // Icon cho "Sản phẩm" - có thể thay: 📱 💻 🎁 🏷️
            };
            
            // Helper functions
            const CHART_COLORS = {
                red: 'rgb(255, 99, 132)',
                orange: 'rgb(255, 159, 64)',
                yellow: 'rgb(255, 205, 86)',
                green: 'rgb(75, 192, 192)',
                blue: 'rgb(54, 162, 235)',
                purple: 'rgb(153, 102, 255)',
                grey: 'rgb(201, 203, 207)'
            };

            function transparentize(color, opacity) {
                const alpha = opacity === undefined ? 0.5 : 1 - opacity;
                return color.replace('rgb', 'rgba').replace(')', `, ${alpha})`);
            }

            function getRandomNumber(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            function generateRandomData(count, min, max) {
                const data = [];
                for (let i = 0; i < count; i++) {
                    data.push(getRandomNumber(min, max));
                }
                return data;
            }

            // Custom tooltip positioner
            Chart.Tooltip.positioners.bottom = function(items) {
                const pos = Chart.Tooltip.positioners.average(items);
                
                if (pos === false) {
                    return false;
                }
                
                const chart = this.chart;
                
                return {
                    x: pos.x,
                    y: chart.chartArea.bottom,
                    xAlign: 'center',
                    yAlign: 'bottom',
                };
            };

            // Data với 7 tháng
            const monthLabels = ['01/01', '02/01', '03/01', '04/01', '05/01', '06/01', '07/01', '08/01', '09/01', '10/01'];
            const totalBillData = [120, 190, 300, 500, 200, 300, 450, 280, 390, 180]; // Tổng giá vốn
            const totalDropshipData = [20, 35, 45, 80, 40, 60, 75, 50, 65, 30]; // Phí Drop
            
            const data = {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Tổng giá vốn',
                        data: totalBillData,
                        fill: false,
                        borderColor: CHART_COLORS.red,
                        backgroundColor: transparentize(CHART_COLORS.red),
                        tension: 0.4
                    },
                    {
                        label: 'Phí Drop', 
                        data: totalDropshipData,
                        fill: false,
                        borderColor: CHART_COLORS.blue,
                        backgroundColor: transparentize(CHART_COLORS.blue),
                        tension: 0.4
                    }
                ]
            };

            // Chart configuration
            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Biểu đồ Tổng giá vốn & Phí Drop'
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                padding: 15,
                                generateLabels: function(chart) {
                                    const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                    const labels = original.call(this, chart);
                                    
                                                                         labels.forEach((label, index) => {
                                         if (index === 0) {
                                             label.text = LEGEND_ICONS.totalBill + ' ' + label.text;  // Icon tiền cho "Tổng giá vốn"
                                         } else if (index === 1) {
                                             label.text = LEGEND_ICONS.feeDrop + ' ' + label.text;  // Icon vận chuyển cho "Phí Drop"
                                         }
                                     });
                                    
                                    return labels;
                                }
                            }
                        },
                        tooltip: {
                            position: 'average'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            };

            // Create chart
            const revenueChart = new Chart(ctx, config);

            // Action buttons functionality (optional)
            function randomizeData() {
                revenueChart.data.datasets.forEach((dataset, index) => {
                    if (index === 0) {
                        // Tổng giá vốn: từ 0-1000 nghìn VNĐ
                        dataset.data = generateRandomData(revenueChart.data.labels.length, 0, 1000);
                    } else {
                        // Phí Drop: từ 0-200 nghìn VNĐ
                        dataset.data = generateRandomData(revenueChart.data.labels.length, 0, 200);
                    }
                });
                revenueChart.update();
            }

            function addDataset() {
                const data = revenueChart.data;
                const colors = Object.values(CHART_COLORS);
                const colorIndex = data.datasets.length % colors.length;
                const dsColor = colors[colorIndex];
                
                const datasetLabels = ['Doanh thu bán hàng', 'Chi phí vận chuyển', 'Lợi nhuận ròng', 'Chi phí quảng cáo'];
                const labelIndex = (data.datasets.length - 2) % datasetLabels.length;
                
                const newDataset = {
                    label: datasetLabels[labelIndex] + ' (nghìn VNĐ)',
                    backgroundColor: transparentize(dsColor, 0.5),
                    borderColor: dsColor,
                    data: generateRandomData(data.labels.length, 0, 300), // Dữ liệu từ 0-300 nghìn VNĐ
                };
                revenueChart.data.datasets.push(newDataset);
                revenueChart.update();
            }

            function addData() {
                const data = revenueChart.data;
                if (data.datasets.length > 0) {
                    // Thêm một ngày mới (giả lập)
                    const today = new Date();
                    const newDate = new Date(today.getTime() + (data.labels.length - 6) * 24 * 60 * 60 * 1000);
                    const dateStr = newDate.getDate().toString().padStart(2, '0') + '/' + 
                                   (newDate.getMonth() + 1).toString().padStart(2, '0');
                    data.labels.push(dateStr);

                    // Thêm dữ liệu ngẫu nhiên cho các dataset
                    for (let index = 0; index < data.datasets.length; ++index) {
                        data.datasets[index].data.push(getRandomNumber(0, 500)); // Giá trị từ 0-500 nghìn VNĐ
                    }

                    revenueChart.update();
                }
            }

            function removeDataset() {
                if (revenueChart.data.datasets.length > 1) {
                    revenueChart.data.datasets.pop();
                    revenueChart.update();
                }
            }

            function removeData() {
                if (revenueChart.data.labels.length > 1) {
                    revenueChart.data.labels.splice(-1, 1); // remove the label first

                    revenueChart.data.datasets.forEach(dataset => {
                        dataset.data.pop();
                    });

                    revenueChart.update();
                }
            }

            // Export functions to window object for button access
            window.chartActions = {
                randomizeData: randomizeData,
                addDataset: addDataset,
                addData: addData,
                removeDataset: removeDataset,
                removeData: removeData
            };

            // ==================== PIE CHART ====================
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            
            // Fake data cho pie chart
            const pieData = {
                labels: ['Đơn hàng', 'Sản phẩm bán ra'],
                datasets: [{
                    label: 'Thống kê 7 ngày',
                    data: [85, 320], // 85 đơn hàng, 320 sản phẩm bán ra
                    backgroundColor: [
                        CHART_COLORS.red,
                        CHART_COLORS.blue,
                        CHART_COLORS.green,
                        CHART_COLORS.orange,
                        CHART_COLORS.purple
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            const pieConfig = {
                type: 'pie',
                data: pieData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                padding: 12,
                                generateLabels: function(chart) {
                                    const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                    const labels = original.call(this, chart);
                                    
                                                                         labels.forEach((label, index) => {
                                         if (label.text === 'Đơn hàng') {
                                             label.text = LEGEND_ICONS.orders + ' ' + label.text;  // Icon đơn hàng
                                         } else if (label.text === 'Sản phẩm bán ra') {
                                             label.text = LEGEND_ICONS.products + ' ' + label.text;  // Icon sản phẩm
                                         }
                                     });
                                    
                                    return labels;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tổng quan: 7 ngày trước'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    
                                    if (label === 'Đơn hàng') {
                                        return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} đơn (${percentage}%)`;
                                    } else {
                                        return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} sản phẩm (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                }
            };

            // Tạo pie chart
            const pieChart = new Chart(pieCtx, pieConfig);

            // Pie chart actions
            function randomizePieData() {
                pieChart.data.datasets[0].data = [
                    getRandomNumber(50, 500), // Đơn hàng
                    getRandomNumber(100, 2000)  // Sản phẩm
                ];
                pieChart.update();
            }

            function addPieDataset() {
                const newDataset = {
                    label: 'Dataset ' + (pieChart.data.datasets.length + 1),
                    data: [getRandomNumber(20, 200), getRandomNumber(50, 800)],
                    backgroundColor: [
                        Object.values(CHART_COLORS)[pieChart.data.datasets.length % Object.values(CHART_COLORS).length],
                        Object.values(CHART_COLORS)[(pieChart.data.datasets.length + 1) % Object.values(CHART_COLORS).length]
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                };
                pieChart.data.datasets.push(newDataset);
                pieChart.update();
            }

            function addPieData() {
                const newLabels = ['Đơn hủy', 'Đơn hoàn', 'Khách hàng mới', 'Doanh thu'];
                const currentLength = pieChart.data.labels.length;
                
                if (currentLength < newLabels.length + 2) {
                    pieChart.data.labels.push(newLabels[currentLength - 2]);
                    pieChart.data.datasets[0].data.push(getRandomNumber(10, 300));
                    pieChart.data.datasets[0].backgroundColor.push(Object.values(CHART_COLORS)[currentLength % Object.values(CHART_COLORS).length]);
                    pieChart.update();
                }
            }

            // Export pie chart actions
            window.pieChartActions = {
                randomizeData: randomizePieData,
                addDataset: addPieDataset,
                addData: addPieData
            };

            // ==================== TIME FILTER & CUSTOM DATE PICKER ====================
            
            // Data for different time periods
            const chartDataByPeriod = {
                today: {
                    line: {
                        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                        totalBill: [10, 25, 40, 85, 120, 95, 60],
                        totalDropship: [2, 5, 8, 15, 22, 18, 12]
                    },
                    pie: {
                        data: [12, 45],
                        title: 'Tổng quan: Hôm nay'
                    }
                },
                yesterday: {
                    line: {
                        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                        totalBill: [15, 30, 45, 90, 130, 100, 80],
                        totalDropship: [3, 6, 9, 18, 25, 20, 16]
                    },
                    pie: {
                        data: [18, 67],
                        title: 'Tổng quan: Hôm qua'
                    }
                },
                '7days': {
                    line: {
                        labels: ['6 ngày trước', '5 ngày trước', '4 ngày trước', '3 ngày trước', '2 ngày trước', 'Hôm qua', 'Hôm nay'],
                        totalBill: [120, 190, 300, 500, 200, 300, 450],
                        totalDropship: [20, 35, 45, 80, 40, 60, 75]
                    },
                    pie: {
                        data: [85, 320],
                        title: 'Tổng quan: 7 ngày trước'
                    }
                },
                '30days': {
                    line: {
                        labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
                        totalBill: [2800, 3200, 2950, 3400],
                        totalDropship: [480, 550, 510, 580]
                    },
                    pie: {
                        data: [342, 1280],
                        title: 'Tổng quan: 30 ngày trước'
                    }
                },
                thismonth: {
                    line: {
                        labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
                        totalBill: [2500, 2800, 3100, 2900],
                        totalDropship: [420, 480, 530, 490]
                    },
                    pie: {
                        data: [298, 1150],
                        title: 'Tổng quan: Tháng này'
                    }
                },
                lastmonth: {
                    line: {
                        labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
                        totalBill: [2200, 2600, 2400, 2800],
                        totalDropship: [380, 440, 410, 470]
                    },
                    pie: {
                        data: [256, 980],
                        title: 'Tổng quan: Tháng trước'
                    }
                }
            };

            // Function to update line chart
            function updateLineChart(period) {
                const data = chartDataByPeriod[period].line;
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.totalBill;
                revenueChart.data.datasets[1].data = data.totalDropship;
                revenueChart.update();
            }

            // Function to update pie chart
            function updatePieChart(period) {
                const data = chartDataByPeriod[period].pie;
                pieChart.data.datasets[0].data = data.data;
                pieChart.options.plugins.title.text = data.title;
                pieChart.update();
            }

            // Function to get period text
            function getPeriodText(period) {
                const periodTexts = {
                    'today': 'Hôm nay',
                    'yesterday': 'Hôm qua',
                    '7days': '7 ngày trước',
                    '30days': '30 ngày trước',
                    'thismonth': 'Tháng này',
                    'lastmonth': 'Tháng trước'
                };
                return periodTexts[period] || '7 ngày trước';
            }

            // Chart time filter event listeners
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('chart-time-filter')) {
                    e.preventDefault();
                    
                    const period = e.target.getAttribute('data-period');
                    const periodText = getPeriodText(period);
                    
                    // Update charts immediately
                    updateLineChart(period);
                    updatePieChart(period);
                    
                    // Update dropdown texts
                    document.getElementById('customLineChartDate').innerHTML = periodText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                    document.getElementById('customPieChartDate').innerHTML = periodText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                    
                    // Close modal
                    const modal = document.getElementById('customDateModal');
                    if (modal) {
                        bootstrap.Modal.getInstance(modal)?.hide();
                    }
                }
            });

            // Initialize charts with 7 days data (default)
            updateLineChart('7days');
            updatePieChart('7days');

            // Custom date picker functionality
            document.addEventListener('click', function(e) {
                if (e.target.id === 'customLineChartDate') {
                    e.preventDefault();
                    openCustomDatePicker('line');
                }
                if (e.target.id === 'customPieChartDate') {
                    e.preventDefault();
                    openCustomDatePicker('pie');
                }
            });

            // Function to open custom date picker
            function openCustomDatePicker(chartType) {
                // Create modal if not exists
                let modal = document.getElementById('customDateModal');
                if (!modal) {
                    modal = createCustomDateModal();
                    document.body.appendChild(modal);
                }
                
                // Reset date range picker
                dateRangePicker.fromDate = null;
                dateRangePicker.toDate = null;
                dateRangePicker.hoveredDate = null;
                
                // Set current chart type
                modal.dataset.chartType = chartType;
                
                // Re-initialize calendars
                setTimeout(() => {
                    initializeDateRangePicker();
                    
                    // Reset to default 7 days state  
                    updateLineChart('7days');
                    updatePieChart('7days');
                    const defaultText = '7 ngày trước';
                    document.getElementById('customLineChartDate').innerHTML = defaultText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                    document.getElementById('customPieChartDate').innerHTML = defaultText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                }, 200);
                
                // Show modal
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            }

                        // Function to create custom date modal
            function createCustomDateModal() {
                const modalHTML = `
                    <div class="modal fade" id="customDateModal" tabindex="-1" aria-labelledby="customDateModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-base" style="max-width: 600px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="customDateModalLabel">Chọn khoảng thời gian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body" style="padding: 0.5rem;">
                                    <form id="customDateForm">                     
                                        <div class="date-range-picker" id="dateRangePicker">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <div class="selected-range-inline">
                                                        <div class="">
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="today" data-chart="line">Hôm nay</a>
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="yesterday" data-chart="line">Hôm qua</a>
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="7days" data-chart="line">7 ngày trước</a>
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="30days" data-chart="line">30 ngày trước</a>
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="thismonth" data-chart="line">Tháng này</a>
                                                            <a class="dropdown-item chart-time-filter" href="#" data-period="lastmonth" data-chart="line">Tháng trước</a>
                                                        <div class="dropdown-divider"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="month-calendar" id="leftMonth"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="month-calendar" id="rightMonth"></div>
                                                </div>
                                            </div>
                                        </div>
                                         
                                                                          </form>
                                    </div>
                                    <div class="modal-footer p-3">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                
                             </div>
                        </div>
                    </div>
                `;
                
                const modalElement = document.createElement('div');
                modalElement.innerHTML = modalHTML;
                
                // Initialize date range picker after modal is created
                setTimeout(() => {
                    initializeDateRangePicker();
                }, 100);
                
                return modalElement.firstElementChild;
            }

            // Date range picker variables
            let dateRangePicker = {
                fromDate: null,
                toDate: null,
                hoveredDate: null,
                currentLeftMonth: new Date(),
                currentRightMonth: new Date()
            };

            // Initialize date range picker
            function initializeDateRangePicker() {
                const today = new Date();
                dateRangePicker.currentLeftMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                dateRangePicker.currentRightMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
                
                renderCalendar('leftMonth', dateRangePicker.currentLeftMonth);
                renderCalendar('rightMonth', dateRangePicker.currentRightMonth);
                
                updateDateDisplay();
            }

            // Render calendar for a specific month
            function renderCalendar(containerId, date) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const year = date.getFullYear();
                const month = date.getMonth();
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());

                // Create header
                const header = document.createElement('div');
                header.className = 'calendar-header';
                header.innerHTML = `
                    <button type="button" class="calendar-nav-btn" onclick="navigateMonth('${containerId}', -1)">
                        <i class="mdi mdi-chevron-left"></i>
                    </button>
                    <div class="calendar-title">
                        ${date.toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' })}
                    </div>
                    <button type="button" class="calendar-nav-btn" onclick="navigateMonth('${containerId}', 1)">
                        <i class="mdi mdi-chevron-right"></i>
                    </button>
                `;

                // Create day headers
                const dayHeaders = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                const grid = document.createElement('div');
                grid.className = 'calendar-grid';

                // Add day headers
                dayHeaders.forEach(day => {
                    const dayHeader = document.createElement('div');
                    dayHeader.className = 'calendar-day-header';
                    dayHeader.textContent = day;
                    grid.appendChild(dayHeader);
                });

                // Add calendar days
                for (let i = 0; i < 42; i++) { // 6 weeks * 7 days
                    const currentDate = new Date(startDate);
                    currentDate.setDate(startDate.getDate() + i);
                    
                    const dayElement = document.createElement('div');
                    dayElement.className = 'custom-day';
                    dayElement.textContent = currentDate.getDate();
                    dayElement.dataset.date = currentDate.toISOString().split('T')[0];
                    
                    // Add classes based on date state
                    if (currentDate.getMonth() !== month) {
                        dayElement.classList.add('outside-month');
                    }
                    
                    updateDayClasses(dayElement, currentDate);
                    
                    // Add event listeners
                    dayElement.addEventListener('click', () => onDateSelection(currentDate));
                    dayElement.addEventListener('mouseenter', () => {
                        dateRangePicker.hoveredDate = currentDate;
                        updateAllCalendars();
                    });
                    dayElement.addEventListener('mouseleave', () => {
                        dateRangePicker.hoveredDate = null;
                        updateAllCalendars();
                    });
                    
                    grid.appendChild(dayElement);
                }

                container.innerHTML = '';
                container.appendChild(header);
                container.appendChild(grid);
            }

            // Update day classes based on selection state
            function updateDayClasses(dayElement, date) {
                const dateStr = date.toISOString().split('T')[0];
                const fromDateStr = dateRangePicker.fromDate ? dateRangePicker.fromDate.toISOString().split('T')[0] : null;
                const toDateStr = dateRangePicker.toDate ? dateRangePicker.toDate.toISOString().split('T')[0] : null;
                const hoveredDateStr = dateRangePicker.hoveredDate ? dateRangePicker.hoveredDate.toISOString().split('T')[0] : null;

                // Reset classes
                dayElement.classList.remove('focused', 'range', 'faded', 'start-range', 'end-range', 'in-range');

                // Check if this day is selected
                if (fromDateStr === dateStr || toDateStr === dateStr) {
                    dayElement.classList.add('focused');
                }

                // Check if this day is in range
                if (fromDateStr && toDateStr) {
                    if (isDateInRange(date, dateRangePicker.fromDate, dateRangePicker.toDate)) {
                        if (fromDateStr === dateStr) {
                            dayElement.classList.add('start-range');
                        } else if (toDateStr === dateStr) {
                            dayElement.classList.add('end-range');
                        } else {
                            dayElement.classList.add('in-range');
                        }
                    }
                } else if (fromDateStr && hoveredDateStr && isDateInRange(date, dateRangePicker.fromDate, dateRangePicker.hoveredDate)) {
                    dayElement.classList.add('faded');
                }
            }

            // Handle date selection
            function onDateSelection(selectedDate) {
                if (!dateRangePicker.fromDate || (dateRangePicker.fromDate && dateRangePicker.toDate)) {
                    // Start new selection
                    dateRangePicker.fromDate = selectedDate;
                    dateRangePicker.toDate = null;
                } else {
                    // Complete the range
                    if (selectedDate < dateRangePicker.fromDate) {
                        dateRangePicker.toDate = dateRangePicker.fromDate;
                        dateRangePicker.fromDate = selectedDate;
                    } else {
                        dateRangePicker.toDate = selectedDate;
                    }
                    
                    // Update charts immediately when range is complete
                    if (dateRangePicker.fromDate && dateRangePicker.toDate) {
                        const startDateStr = dateRangePicker.fromDate.toISOString().split('T')[0];
                        const endDateStr = dateRangePicker.toDate.toISOString().split('T')[0];
                        const customData = generateCustomDateData(startDateStr, endDateStr);
                        const dateRangeText = formatDateRange(startDateStr, endDateStr);
                        
                        // Update both charts
                        updateLineChartWithCustomData(customData.line);
                        updatePieChartWithCustomData(customData.pie);
                        
                        // Update dropdown texts
                        document.getElementById('customLineChartDate').innerHTML = dateRangeText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                        document.getElementById('customPieChartDate').innerHTML = dateRangeText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                        
                        // Close modal after a short delay to show selection
                        setTimeout(() => {
                            const modal = document.getElementById('customDateModal');
                            if (modal) {
                                bootstrap.Modal.getInstance(modal)?.hide();
                            }
                        }, 500);
                    }
                }
                
                updateAllCalendars();
                updateDateDisplay();
            }

            // Update all calendars
            function updateAllCalendars() {
                const leftContainer = document.getElementById('leftMonth');
                const rightContainer = document.getElementById('rightMonth');
                
                if (leftContainer) {
                    const days = leftContainer.querySelectorAll('.custom-day');
                    days.forEach(dayElement => {
                        const date = new Date(dayElement.dataset.date);
                        updateDayClasses(dayElement, date);
                    });
                }
                
                if (rightContainer) {
                    const days = rightContainer.querySelectorAll('.custom-day');
                    days.forEach(dayElement => {
                        const date = new Date(dayElement.dataset.date);
                        updateDayClasses(dayElement, date);
                    });
                }
            }

            // Navigate months
            function navigateMonth(containerId, direction) {
                if (containerId === 'leftMonth') {
                    dateRangePicker.currentLeftMonth.setMonth(dateRangePicker.currentLeftMonth.getMonth() + direction);
                    renderCalendar('leftMonth', dateRangePicker.currentLeftMonth);
                } else if (containerId === 'rightMonth') {
                    dateRangePicker.currentRightMonth.setMonth(dateRangePicker.currentRightMonth.getMonth() + direction);
                    renderCalendar('rightMonth', dateRangePicker.currentRightMonth);
                }
            }

            // Check if date is in range
            function isDateInRange(date, start, end) {
                if (!start || !end) return false;
                return date >= start && date <= end;
            }

            // Update date display
            function updateDateDisplay() {
                const fromDisplay = document.getElementById('fromDateDisplay');
                const toDisplay = document.getElementById('toDateDisplay');
                
                if (fromDisplay) {
                    fromDisplay.textContent = dateRangePicker.fromDate 
                        ? JSON.stringify(dateRangePicker.fromDate.toISOString().split('T')[0]).replace(/"/g, '') 
                        : 'null';
                }
                
                if (toDisplay) {
                    toDisplay.textContent = dateRangePicker.toDate 
                        ? JSON.stringify(dateRangePicker.toDate.toISOString().split('T')[0]).replace(/"/g, '') 
                        : 'null';
                }
            }

            // Make navigateMonth available globally
            window.navigateMonth = navigateMonth;

            // No need for apply custom date - charts update immediately on selection

            // Function to generate custom data based on date range
            function generateCustomDateData(startDate, endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const daysDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                // Generate labels based on date range
                const labels = [];
                const lineData = [];
                const lineDropshipData = [];
                
                for (let i = 0; i < daysDiff; i++) {
                    const currentDate = new Date(start);
                    currentDate.setDate(start.getDate() + i);
                    labels.push(currentDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
                    
                    // Generate random data for demo
                    lineData.push(getRandomNumber(50, 800));
                    lineDropshipData.push(getRandomNumber(10, 150));
                }

                // Generate pie data
                const totalOrders = getRandomNumber(daysDiff * 5, daysDiff * 20);
                const totalProducts = getRandomNumber(daysDiff * 15, daysDiff * 60);

                return {
                    line: {
                        labels: labels,
                        totalBill: lineData,
                        totalDropship: lineDropshipData
                    },
                    pie: {
                        data: [totalOrders, totalProducts],
                        title: `Tùy chọn: ${formatDateRange(startDate, endDate)}`
                    }
                };
            }

            // Function to format date range text
            function formatDateRange(startDate, endDate) {
                const start = new Date(startDate).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                const end = new Date(endDate).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                return `${start} - ${end}`;
            }

            // Function to update line chart with custom data
            function updateLineChartWithCustomData(customData) {
                revenueChart.data.labels = customData.labels;
                revenueChart.data.datasets[0].data = customData.totalBill;
                revenueChart.data.datasets[1].data = customData.totalDropship;
                revenueChart.update();
            }

            // Function to update pie chart with custom data
            function updatePieChartWithCustomData(customData) {
                pieChart.data.datasets[0].data = customData.data;
                pieChart.options.plugins.title.text = customData.title;
                pieChart.update();
            }
        });
    </script>


    @endsection