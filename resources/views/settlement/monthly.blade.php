@extends('layout')
@section('title', 'Quyết toán tháng')

@section('main')
<style>
    .modal-dialog-right {
        position: fixed;
        right: 0;
        margin: 0;
        height: 100%;
        width: 500px;
        transform: translateX(100%);
    }

    .card {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container bg-white">
    <h4 class="pt-2 text-body fw-light">Quyết toán</h4>
    <div class="bg-white mt-2 p-4 rounded-3">

        {{-- THỐNG KÊ THÁNG TRƯỚC --}}
        @if(isset($quyet_toan_thang_truoc))
        <div class="row align-items-center border-2">
            <div class="col-md-3 align-items-center">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <div>
                        <h6 class="text-body-emphasis mb-1 fw-bold">
                            <img class="pe-1" src="assets/images/svg/crypto-icons/amp.svg" alt="File Icon" style="width:10%;">
                            Chênh lệch
                            <i class="bi bi-exclamation-circle-fill text-body-secondary" role="button" data-bs-toggle="tooltip"
                                title="Nếu số tiền là (- âm) thì bạn sẽ có giao dịch trừ phần âm đó , Nếu là (+ dương) bạn sẽ có giao dịch được trả thêm phần đó"></i>
                        </h6>
                        <div class="small text-success mb-1" style="font-size: 10px;">
                            Tháng trước
                            <i class="bi bi-question-circle-fill text-body-tertiary ms-1" role="button" data-bs-toggle="tooltip"
                                title="{{ \Carbon\Carbon::parse($quyet_toan_thang_truoc->month . '-01')->format('01/m/Y') }} ~ {{ \Carbon\Carbon::parse($quyet_toan_thang_truoc->month . '-01')->endOfMonth()->format('d/m/Y') }}">
                            </i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->tien_phai_thanh_toan) }}đ
                    </h4>
                </div>
            </div>

            <div class="col-auto fw-bold fs-4 mb-3">=</div>

            <div class="col-md-3">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <h6 class="text-body-emphasis mb-1 fw-bold">
                        <img class="pe-1" src="https://salework.net/assets/images/apps/stock.png" alt="File Icon" style="width:10%;">
                        Thực tế
                        <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                            title="= (Tổng số tiền vốn của tất cả sản phẩm được giao thành công + Tổng tiền dropship đã thanh toán trên web)"></i>
                    </h6>
                    <div class="small text-white mb-1" style="font-size: 10px;">i</div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->tien_thuc_te + $quyet_toan_thang_truoc->khau_trang) }}đ
                    </h4>
                </div>
            </div>

            <div class="col-auto fw-bold fs-4 mb-3">-</div>

            <div class="col-md-3">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <h6 class="text-body-emphasis mb-1 fw-bold">
                        <img class="pe-1" src="https://img.icons8.com/windows/32/blog-logo.png" alt="File Icon" style="width:10%;">Đã thu
                        <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                            title="= (Số tiền đã thanh toán đơn sỉ - (Tiền sản phẩm huỷ + phí dropship của sản phẩm huỷ) - Giá vốn của sản phẩm đơn hoàn)">
                        </i>
                    </h6>
                    <div class="small text-white mb-1" style="font-size: 10px;">i</div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->total_chi) }}đ
                    </h4>
                </div>
            </div>
        </div>
        @endif

        {{-- BỘ LỌC --}}
        <div class="row mt-4">
            <div class="col-md-3">
                <input type="month" class="form-control" placeholder="Chọn tháng">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Tìm kiếm ID quyết toán/điều chỉnh">
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-success" type="button">
                    Lọc
                </button>
            </div>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Mã quyết toán</th>
                        <th>Ngày tạo</th>
                        <th>Tháng quyết toán</th>
                        <th>Đã thu</th>
                        <th>Thực tế(web)</th>
                        <th>Thực tế(salework)</th>
                        <th>Chênh lệch</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quyet_toan as $item)
                    <tr>
                        <td>{{ $item->id_QT }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->month }}</td>
                        <td>{{ number_format($item->total_paid) }}đ</td>
                        <td>{{ number_format($item->total_chi) }}đ</td>
                        <td> {{ number_format($item->tien_thuc_te + $item->khau_trang) }}đ</td>
                        <td>{{ number_format($item->tien_phai_thanh_toan) }}đ</td>
                        <td>
                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                Xem chi tiết
                            </a>

                            {{-- MODAL --}}
                            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-right col-12 col-md-12 m-0" style="width: 40%; max-width: none;">
                                    <div class="modal-content h-100">
                                        <div class="modal-header flex-column align-items-start">
                                            <h5 class="modal-title" id="modalLabel{{ $item->id }}">
                                                Phân tích quyết toán tháng {{ $item->month }}
                                            </h5>
                                            <h5 class="fw-bolder text-start text-body-emphasis mb-1 p-2">
                                                ID quyết toán: {{ $item->id_QT }}
                                            </h5>
                                            <h5 class="fw-bolder text-start text-body-emphasis mb-1 p-2">
                                                Ngày lọc <br>
                                                <span class="fw-normal m-2" style="font-size-adjust: 10px;">1-3-2025 ~ 31-3-2025</span>
                                            </h5>
                                        </div>

                                        <div class="modal-body overflow-auto">
                                            <div class="p-3 bg-white">
                                                <p><strong>Tổng tiền đã nạp:</strong> <span class="text-success fw-bold">{{ number_format($item->total_topup) }} VND</span></p>
                                                <p><strong>Thanh toán đơn hàng:</strong> <span class="text-primary fw-bold">{{ number_format($item->total_paid) }} VND</span></p>
                                                <p><strong>Thanh toán quảng cáo:</strong> <span class="text-primary fw-bold">{{ number_format($item->total_paid_ads) }} VND</span></p>
                                                <p><strong>Đơn đã huỷ:</strong> <span class="text-danger fw-bold">{{ number_format($item->total_canceled) }} VND</span></p>
                                                <p><strong>Đơn hoàn:</strong> <span class="text-danger fw-bold">{{ number_format($item->total_return) }} VND</span></p>
                                                <p><strong>Tiền Dropships:</strong> <span class="text-danger fw-bold">{{ number_format($item->Drop_ships) }} VND</span></p>
                                                @if (!empty($item->shop_details))
                                                <div class="mt-3">
                                                    <strong>Chi tiết hoàn theo shop:</strong>
                                                    <ul class="ps-3">
                                                        @foreach($item->shop_details as $shop)
                                                        <li>
                                                            Shop: <strong>
                                                            @foreach($shops as $shop1)
                                                            @if($shop['shop_id'] == $shop1->shop_id)
                                                            {{ $shop1->shop_name }}
                                                            @endif
                                                            @endforeach
                                                            </strong> — Hoàn: <span class="fw-bold text-danger">{{ number_format($shop['tong_tien_hoan']) }} VND</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif

                                                <p><strong>Tổng chi:</strong> <span class="text-danger fw-bold">{{ number_format($item->total_chi) }} VND</span></p>
                                                <p><strong>Khẩu trang hoàn:</strong> <span class="text-dark fw-bold">{{ number_format($item->khau_trang) }} VND</span></p>
                                                <p><strong>Tổng tiền thực tế phải trả:</strong> <span class="text-dark fw-bold">{{ number_format($item->tien_thuc_te) }} VND</span></p>
                                                <p><strong>Chênh lệch:</strong> <span class="fw-bold">{{ number_format($item->tien_phai_thanh_toan) }} VND</span></p>
                                            </div>
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

        {{-- PHÂN TRANG --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Hiển thị 5/Trang</div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">»</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection