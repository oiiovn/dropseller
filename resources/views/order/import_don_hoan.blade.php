@extends('layout')
@section('title', 'main')
@section('main')
<form action="{{ url('/import-don-hoan') }}" method="POST" enctype="multipart/form-data" class="upload-form shadow p-4 rounded bg-white border">
    @csrf

    <div class="mb-3">
        <label for="file" class="form-label fw-bold">📁 Chọn file Excel:</label>
        <input type="file" class="form-control" name="file" id="file" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        🚀 Tải lên và xử lý
    </button>
</form>


@if (isset($ketQua))
<div class="container-fluid bg-white">

    <div class="d-flex justify-content-between align-items-center p-2 mt-4 mb-3">
        <h4 class="fw-bold mb-0">📊 Kết quả sản phẩm hoàn:</h4>

        {{-- Nút bên phải --}}
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTaoDonHoan">
            📥 Tạo đơn hoàn
        </button>

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

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle text-center mb-0">
            <thead class="table-dark">
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
                    <td>{{ $item['ngay'] }}</td>
                    <td>{{ $item['shop_id'] }}</td>
                    <td>{{ $item['order_code'] }}</td>
                    <td>{{ $item['filter_date'] }}</td>
                    <td>{{ $item['sku'] }}</td>
                    <td>{!! $item['ket_qua'] !!}</td> {{-- Cho phép emoji hoặc icon HTML --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endif
@endsection