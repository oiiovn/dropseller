@extends('layout')
@section('title', 'main')
<div style="height: 100vh; overflow: hidden;">
    @section('main')
</div>
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
<div class="container-fluid bg-white mt-3 rounded-3 shadow" style="max-height: calc(100vh - 50px); ">

    <div class="d-flex justify-content-between align-items-center   rounded-3">
        <!-- Modal -->
        <div class="modal fade" id="modalTaoDonHoan" tabindex="-1" aria-labelledby="modalTaoDonHoanLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen modal-dialog-centered p-5">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTaoDonHoanLabel">📥 Tạo đơn hoàn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid py-2">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Tổng số sản phẩm ({{$tongSanPham}})</h5>
                                <div>
                                    <a href="#" class="btn btn-primary">📥 Tạo đơn hoàn</a>
                                </div>
                            </div>

                            <div class="row g-3">
                                @foreach($sanPhamGop as $item)
                                <div class="col-sm-12 col-md-8 col-lg-6 col-xl-4">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ $item['image'] }}" class="card-img-top object-fit-cover " style="width:50px; height:50px;" alt="{{ $item['sku'] }}">
                                        <div class="card-body p-2">
                                            <h6 class="fw-bold text-primary mb-1">{{ $item['sku'] }}</h6>
                                            <p class="small text-muted mb-2 text-truncate" title="{{ $item['product_name'] }}">
                                                {{ $item['product_name'] }}
                                            </p>
                                            <p class="mb-0"><strong>Số lượng:</strong> {{ $item['so_luong'] }}</p>
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

    <div class="table-responsive shadow-sm rounded-3">
        <table class="table table-bordered table-striped align-middle text-center mb-0">
            <div class="col-12 d-flex justify-content-between align-items-center  p-2 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0 text-muted">Danh sách sản phẩm hoàn :</h4>
                </div>
                {{-- Nút bên phải ngoài cùng --}}
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTaoDonHoan">
                        Tạo đơn hoàn
                    </button>
            </div>
            <thead class="bg-primary-subtle">
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
                    <td>{{ $item['shop_id'] }}</td>
                    <td>{{ $item['order_code'] }}</td>
                    <td class="col-2">{{ $item['filter_date'] }}</td>
                    <td>{{ $item['sku'] }}</td>
                    <td class="col-1">{!! $item['ket_qua'] !!}</td> {{-- Cho phép emoji hoặc icon HTML --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endif
@endsection

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
        background-color: #f8f9fa;
        /* Màu nền cho header */
    }

    .bg-primary-subtle {
        background-color: #e9ecef;
        /* Màu nền header */
    }
</style>