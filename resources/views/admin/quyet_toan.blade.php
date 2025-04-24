@extends('layout')
@section('title', 'Danh sách báo cáo tháng')

@section('main')
<div class="container-fluid bg-white">
<h4 class="pt-2">Quyết Toán </h4>
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th scope="col">Mã quyết toán</th>
                    <th>Người dùng</th>
                    <th>Tháng quyết toán</th>
                    <th>Đã chi(Web)</th>
                    <th>Đơn huỷ(Web)</th>
                    <th>Đơn hoàn</th>
                    <th>Quà hoàn(salework)</th>
                    <th>Thực tế(salework)</th>
                    <th>Chênh lệch</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userMonthlyReports as $item)
                <tr>
                <td class="id text-black-50" style="max-width: 5px;">
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li class="hienthicopy">
                                    <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$item->id_QT}}">
                                    {{ $item->id_QT }}
                                        <span class="ri-checkbox-multiple-blank-line icon"></span>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-body-secondary" style="font-size: 11px;">{{ $item->created_at }}</a>
                                </li>
                            </ul>
                        </td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->month }}</td>
                    <td>{{ number_format($item->total_chi) }}đ</td>
                    <td>{{ number_format($item->khau_trang) }}đ</td>
                    <td>{{ number_format($item->tien_thuc_te) }}đ</td>
                    <td>{{ number_format($item->tien_phai_thanh_toan) }}đ</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Cập nhật</button>

                        <!-- Modal -->
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('user-monthly-reports.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Cập nhật báo cáo tháng {{ $item->month }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Tổng chi (Web)</label>
                                                <input type="number" name="total_chi" class="form-control" value="{{ $item->total_chi }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Quà hoàn (Salework)</label>
                                                <input type="number" name="khau_trang" class="form-control" value="{{ $item->khau_trang }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Thực tế (Salework)</label>
                                                <input type="number" name="tien_thuc_te" class="form-control" value="{{ $item->tien_thuc_te }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Lưu</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection