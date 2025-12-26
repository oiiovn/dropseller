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

            <div class="h-100 pt-2">
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
                                            <span class="d-inline-block text-nowrap" id="totalBillPaid">
                                                0 <span class="text-muted">VNĐ</span>
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
                                            <span class="d-inline-block text-nowrap" id="totalOrders">
                                                0 <span class="text-muted">Đơn</span>
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
                                            <span class="d-inline-block text-nowrap" id="totalQuantitySold">
                                                0 <span class="text-muted">Sản phẩm</span>
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
                                            <span class="d-inline-block text-nowrap" id="totalDropship">
                                                0 <span class="text-muted">VNĐ</span>
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

@push('scripts')
<script>
    function updateStatisticsFromChart(data) {
        // Cập nhật giá trị cho các card từ dữ liệu biểu đồ
        document.getElementById('totalBillPaid').innerHTML = 
            `${new Intl.NumberFormat('vi-VN').format(data.total_bill_paid)} <span class="text-muted">VNĐ</span>`;
        
        document.getElementById('totalOrders').innerHTML = 
            `${new Intl.NumberFormat('vi-VN').format(data.total_orders)} <span class="text-muted">Đơn</span>`;
        
        document.getElementById('totalQuantitySold').innerHTML = 
            `${new Intl.NumberFormat('vi-VN').format(data.total_quantity_sold)} <span class="text-muted">Sản phẩm</span>`;
        
        document.getElementById('totalDropship').innerHTML = 
            `${new Intl.NumberFormat('vi-VN').format(data.total_dropship)} <span class="text-muted">VNĐ</span>`;
    }

    // Lắng nghe sự kiện dữ liệu được cập nhật từ biểu đồ
    window.addEventListener('dashboardDataUpdated', function(event) {
        updateStatisticsFromChart(event.detail);
    });

    // Kiểm tra nếu đã có dữ liệu sẵn từ biểu đồ
    document.addEventListener('DOMContentLoaded', function() {
        if (window.dashboardData) {
            updateStatisticsFromChart(window.dashboardData);
        }
    });
</script>
@endpush
                <!-- Charts Component -->
                @include('components.dashboard-charts')
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

    


    @endsection