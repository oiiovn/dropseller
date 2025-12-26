@extends('layout')
@section('title', 'main')
<div style="height: 100vh; overflow: hidden;">
    @section('main')

    <form action="{{ url('/import-don-hoan') }}" method="POST" enctype="multipart/form-data" class="upload-form shadow p-2 ps-4 pe-4 rounded bg-white border">
        @csrf
        <div class="mb-2 w-50">
            <label for="file" class="form-label fw-bold"><img src="https://salework.net/assets/images/apps/stock.png" alt="File Icon" style="width:4%;"> Chọn file Excel đơn hoàn Salework:</label>
            <div class="d-flex align-items-center" style="gap: 20px;">
                <input type="file" class="form-control" name="file" id="file" required style="max-width: 300px;">
                <button type="submit" class="btn btn-success">
                    Tải lên và xử lý
                </button>
            </div>
        </div>
    </form>
    @if (isset($ketQua))
    <div class="container-fluid bg-white p-2 mt-3 rounded-3 shadow" style="max-height: calc(100vh - 50px); ">
        <div class="d-flex justify-content-between align-items-center   rounded-3">
            <!-- Modal thống kê sản phẩm -->
            <div class="modal fade " id="modalTaoDonHoan" tabindex="-1" aria-labelledby="modalTaoDonHoanLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen modal-dialog-centered p-5">
                    <div class="modal-content rounded-3">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTaoDonHoanLabel">Tổng số sản phẩm ({{$tongSanPham}})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body pt-0">
                            <div class="container-fluid py-2 sticky-top">
                                <div class="col-12 d-flex justify-content-between align-items-center mb-1 bg-white sticky-top py-2" style="z-index: 10;">
                                    <div class="ms-auto">
                                        @if (!empty($ketQua))
                                        <form id="taoThanhToanForm" action="{{ route('order.taoThanhToan') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="data" value="{{ base64_encode(serialize($ketQua)) }}">
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalXacNhan">
                                                💰 Tạo thanh toán
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="row g-3">
                                    @foreach($sanPhamGop as $item)
                                    <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 overflow-y: auto">
                                        <div class="card shadow-lg p-2">
                                            <div class="d-flex align-items-center gap-1">
                                                <img src="{{ $item['image'] }}" class="object-fit-cover rounded" style="width:100px; height:100px;" alt="{{ $item['sku'] }}">
                                                <div style="min-width: 0;">
                                                    <h6 class="fw-bold text-primary mb-1">{{ $item['sku'] }}</h6>
                                                    <p class="small text-muted mb-1 text-truncate" title="{{ $item['product_name'] }}" style="max-width: 100%;">
                                                        {{ $item['product_name'] }}
                                                    </p>
                                                    <p class="mb-0"><strong>Số lượng:</strong> {{ $item['so_luong'] }}</p>
                                                </div>
                                            </div>
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
        <!-- Modal xác nhận -->
        <div class="modal fade" id="modalXacNhan" tabindex="-1" aria-labelledby="modalXacNhanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalXacNhanLabel">Xác nhận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="dongY">
                            <label class="form-check-label" for="dongY">
                                Xác nhận đã kiểm tra đơn hoàn từ salework kho
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="kiemTra">
                            <label class="form-check-label" for="kiemTra">
                                Đồng ý việc tạo đơn hoàn sẽ không được hoàn lại
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-success" onclick="xacNhanTaoThanhToan()">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phần bảng danh sách đươn hoàn -->
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-bordered table-striped align-middle text-center mb-0">
                <div class="col-12 d-flex justify-content-between align-items-center  p-2 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0 text-muted">Danh sách sản phẩm hoàn :</h4>
                    </div>
                    {{-- Nút bên phải ngoài cùng --}}
                    <button class="btn btn-success me-md-4 " data-bs-toggle="modal" data-bs-target="#modalTaoDonHoan">
                        <svg class="theme-arco-icon theme-arco-icon-add " width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.6661 2.33403C8.6661 2.14995 8.51688 2.00073 8.3328 2.00073H7.66622C7.48215 2.00073 7.33293 2.14995 7.33293 2.33403V7.33341H2.33354C2.14946 7.33341 2.00024 7.48263 2.00024 7.66671V8.33329C2.00024 8.51736 2.14946 8.66658 2.33354 8.66658H7.33293V13.666C7.33293 13.85 7.48215 13.9993 7.66622 13.9993H8.3328C8.51688 13.9993 8.6661 13.85 8.6661 13.666V8.66658H13.6655C13.8496 8.66658 13.9988 8.51736 13.9988 8.33329V7.66671C13.9988 7.48263 13.8496 7.33341 13.6655 7.33341H8.6661V2.33403Z" fill-opacity="1"></path>
                        </svg> Tạo đơn hoàn
                    </button>
                </div>
                <thead class="bg-success">
                    <tr>
                        <th>Ngày</th>
                        <th>Shop ID</th>
                        <th>Mã đơn</th>
                        <th>Ngày lọc</th>
                        <th>SKU</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ketQua as $item)
                    <tr>
                        <td class="col-1">{{ $item['ngay'] }}</td>
                        <td class="col-2">
                            @php
                            $shopName = $item['shop_id'];
                            foreach ($shops as $shop) {
                            if ($item['shop_id'] == $shop['shop_id']) {
                            $shopName = $shop['shop_name'];
                            break;
                            }
                            }
                            @endphp
                            {{ $shopName }}
                        </td>

                        <td class="col-1">{{ $item['order_code'] }}</td>
                        <td class="col-2">{{ $item['filter_date'] }}</td>
                        <td class="text-start col-5">{{ $item['sku'] }}</td>
                        <td class="col-1">{!! $item['ket_qua'] !!}</td> {{-- Cho phép emoji hoặc icon HTML --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
    .table-responsive {
        position: relative;
        max-height: 78vh;
        /* Chiều cao tối đa của bảng */
        overflow-y: auto;
        /* Kích hoạt thanh cuộn dọc */
    }

    .table-bordered thead th {
        position: sticky;
        top: -1;
        /* Giữ cố định ở trên cùng */
        z-index: 1;
        /* Đảm bảo header nằm trên nội dung */
        background-color: rgb(124, 179, 234);
        /* Màu nền cho header */
    }

    .bg-success {
        background-color: rgb(107, 172, 237);
        /* Màu nền header */
    }
</style>
<script>
    function xacNhanTaoThanhToan() {
        const dongY = document.getElementById('dongY').checked;
        const kiemTra = document.getElementById('kiemTra').checked;

        if (!dongY || !kiemTra) {
            alert("⚠️ Vui lòng xác nhận đủ cả 2 điều kiện trước khi tạo thanh toán!");
            return;
        }

        // Submit form gốc
        document.getElementById('taoThanhToanForm').submit();
    }
</script>

@endsection