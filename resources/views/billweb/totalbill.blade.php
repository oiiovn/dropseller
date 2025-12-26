@extends('layout')

@section('title', 'Tổng tiền dropship')

@section('main')
<div class="container p-4 bg-white shadow rounded">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Tổng tiền dropship theo khoảng ngày</h3>
        </div>
        <div class="col-md-4 text-end">
            <form method="POST" action="{{ route('export.totalbill') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                <button type="submit" class="btn btn-success">Xuất Excel</button>
            </form>
        </div>
    </div>



    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Từ ngày:</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <label>Đến ngày:</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Lọc dữ liệu</button>
        </div>
    </form>
    <div class="alert alert-info">
        <h5><strong>Thống kê từ ngày</strong> {{ $startDate->format('d/m/Y') }} <strong>đến</strong> {{ $endDate->format('d/m/Y') }}</h5>
        <hr>

        <div class="mb-2">
            <strong>Tổng tiền Dropship:</strong> <span class="text-primary">{{ number_format($total_dropship) }} VND</span><br>
            <strong>Mỗi người nhận từ Dropship:</strong> <span class="text-success">{{ number_format($total_dropship_web) }} VND</span>
        </div>

        <div class="mb-2">
            <strong>Tổng tiền Chương trình đã thanh toán:</strong> <span class="text-primary">{{ number_format($total_program) }} VND</span><br>
            <strong>Mỗi người nhận từ Chương trình:</strong> <span class="text-success">{{ number_format($share_total_program) }} VND</span>
        </div>

        <hr>
        <div>
            <strong class="text-danger">Tổng cộng mỗi người nhận được:</strong>
            <h4 class="mt-2 text-danger">{{ number_format($total_dropship_web + $share_total_program) }} VND</h4>
        </div>
    </div>



</div>
@endsection