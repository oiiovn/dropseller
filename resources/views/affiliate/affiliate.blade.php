@extends('layout')

@section('title', 'Danh sách chương trình')

@section('main')
<div class="container-fluid py-3">
    <h3 class="text-muted fw-normal	mb-2">Tổng quan hoa hồng tiếp thị</h3>

    <div class="row px-3">
        {{-- CỘT TRÁI: Thống kê hoa hồng --}}
        <div class="col-lg-9 mb-2">
            <div class=" p-4 rounded">
                <div class="row g-3">
                    {{-- Card 1 --}}
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate bg-opacity-25	">
                            <div class="card-body">
                                <p class="fw-medium text-muted mb-0">Kế hoạch</p>
                                <h5 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="4000000">0</span>đ
                                </h5>
                                <p class="mb-0 text-muted">
                                    <span class="badge bg-light text-success mb-0">
                                        <i class="ri-arrow-up-line align-middle"></i> 16.24%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate bg-opacity-25">
                            <div class="card-body">
                                <p class="fw-medium text-success mb-0">Đang hoạt động</p>
                                <h5 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="450300">0</span>k
                                </h5>
                                <p class="mb-0 text-muted">
                                    <span class="badge bg-light text-danger mb-0">
                                        <i class="ri-arrow-down-line align-middle"></i> 3.96%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate bg-opacity-25">
                            <div class="card-body">
                                <p class="fw-medium text-danger mb-0">Ngừng hoạt động</p>
                                <h5 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="0">0</span>đ
                                </h5>
                                <p class="mb-0 text-muted">
                                    <span class="badge bg-light text-danger mb-0">
                                        <i class="ri-arrow-down-line align-middle"></i> 0.24%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4 --}}
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate bg-opacity-25">
                            <div class="card-body">
                                <p class="fw-medium text-info mb-0">Hoàn tất kế hoạch</p>
                                <h5 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="3549700">0</span>đ
                                </h5>
                                <p class="mb-0 text-muted">
                                    <span class="badge bg-light text-success mb-0">
                                        <i class="ri-arrow-up-line align-middle"></i> 7.05%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div> <!-- end bg-info -->
        </div> <!-- end col-lg-9 -->

        {{-- CỘT PHẢI: Thông tin phụ --}}
        <div class="col-lg-3">
            <div class="bg-warning-subtle rounded shadow p-4 h-100">
                <h5 class="fw-bold mb-3">Thông tin tổng hợp</h5>
                <ul class="list-unstyled">
                    <li>- Bạn đã mời: <strong>8<b> tore</b></strong></li>
                    <li>- Nhà bán hàng hợp lệ: <strong>8<b> Store</b></strong></li>
                    <li>- Bị huỷ: <strong>0<b> Store</b></strong></li>
                    <li>- Hoa hồng đang chờ: <strong>450,300đ</strong></li>
                    <li>- Tỷ lệ hoàn thành: <strong>75%</strong></li>
                </ul>
                <p class="mt-3 text-muted small">
                    * Dữ liệu được cập nhật theo thời gian thực.<br>
                    <strong>Cập nhật lúc:</strong> {{ now()->format('H:i d/m/Y') }}
                </p>
            </div>
        </div>
    </div>


@endsection