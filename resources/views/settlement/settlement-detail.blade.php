@extends('layout')
@section('title', 'Quyết toán tháng')

@section('main')
<div class="container-pluid p-5 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold">Quyết toán theo tháng</h3>
    </div>

    <form method="GET" class="row g-3 align-items-center mb-4">
        <div class="col-md-3">
            <label for="month" class="form-label">Chọn tháng</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
        </div>
        <div class="col-md-2 d-flex align-items-end pt-4">
            <button type="submit" class="btn btn-primary w-100">Lọc dữ liệu</button>
        </div>
    </form>

    <div class="p-4 bg-light border rounded">
        <h5 class="fw-semibold mb-3">Thống kê tháng {{ \Carbon\Carbon::parse($month)->format('m/Y') }}</h5>

        <p class="mb-2 text-dark">
            <strong class="text-dark">Tổng tiền đã nạp:</strong>
            <span class="text-success fw-bold">{{ number_format($totalTopup, 0, ',', '.') }} VND</span>
        </p>

        <p class="mb-2 text-dark">
            <strong class="text-dark">Tổng tiền đã thanh toán đơn hàng:</strong>
            <span class="text-primary fw-bold">{{ number_format($totalPaid, 0, ',', '.') }} VND</span>
        </p>

        <p class="mb-2 text-dark">
            <strong class="text-dark">Tổng tiền đã thanh toán Quảng cáo:</strong>
            <span class="text-primary fw-bold">{{ number_format($totalPaid_ads, 0, ',', '.') }} VND</span>
        </p>

        <p class="mb-2 text-dark">
            <strong class="text-dark">Tổng tiền đơn đã huỷ:</strong>
            <span class="text-danger fw-bold">{{ number_format($totalCanceled, 0, ',', '.') }} VND</span>
        </p>

        @php
        $userReturnData = $gopTheoUser->firstWhere('user_id', Auth::id());
        @endphp

        <p class="mb-2 text-dark">
            <strong class="text-dark">Tổng tiền đơn hoàn:</strong>
            <span class="text-danger fw-bold">
                {{ number_format($userReturnData['tong_tien_user'] ?? 0, 0, ',', '.') }} VND
            </span>
        </p>

        <div class="mb-2 ms-3">
            <strong>Chi tiết hoàn theo shop:</strong>
            <ul class="ps-3 mb-0">
                @foreach($userReturnData['shops'] ?? [] as $shop)
                <li>
                    Shop ID: <strong>{{ $shop['shop_id'] }}</strong> —
                    Hoàn: <span class="fw-bold text-danger">{{ number_format($shop['tong_tien_hoan'], 0, ',', '.') }} VND</span>
                </li>
                @endforeach
            </ul>
        </div>


        <p class="mb-2 text-dark mt-3">
            <strong class="text-dark">Tổng tiền đã chi tháng {{ \Carbon\Carbon::parse($month)->format('m/Y') }}:</strong>
            <span class="text-danger fw-bold">{{ number_format($total_chi, 0, ',', '.') }} VND</span>
        </p>

        <p class="mb-2 text-dark">
            <strong class="text-dark">Số dư cuối tháng:</strong>
            <span class="text-success fw-bold">{{ number_format($ending_balance ?? 0, 0, ',', '.') }} VND</span>
        </p>
    </div>
</div>
@endsection