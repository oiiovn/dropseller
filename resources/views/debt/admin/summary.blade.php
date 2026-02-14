@extends('debt.admin.layout')

@section('title', 'Tổng quan nợ')

@section('content')
    <h4 class="mb-4">Tổng quan nợ</h4>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="debt-card p-3 h-100">
                <div class="text-muted small">Tổng nợ (theo kế hoạch)</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($totalDebt, 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="debt-card p-3 h-100">
                <div class="text-muted small">Đã thanh toán</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($totalPaid, 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="debt-card p-3 h-100">
                <div class="text-muted small">Còn lại</div>
                <div class="fs-4 fw-bold">{{ number_format($totalRemaining, 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="debt-card p-3 h-100">
                <div class="text-muted small">Số kỳ đã trả / chờ</div>
                <div class="fs-4 fw-bold"><span class="text-success">{{ $paidCount }}</span> / <span class="text-warning">{{ $pendingCount }}</span></div>
            </div>
        </div>
    </div>
    <div class="debt-card overflow-hidden">
        <h6 class="p-3 border-bottom mb-0">Chi tiết theo chủ nợ</h6>
        <table class="table table-hover mb-0">
            <thead><tr><th>Chủ nợ</th><th>Tổng nợ</th><th>Đã trả</th><th>Còn lại</th></tr></thead>
            <tbody>
                @forelse($creditors as $c)
                    <tr>
                        <td>{{ $c->user->name }}</td>
                        <td>{{ number_format($c->total_debt, 0, ',', '.') }} đ</td>
                        <td class="text-success">{{ number_format($c->paid, 0, ',', '.') }} đ</td>
                        <td>{{ number_format($c->remaining, 0, ',', '.') }} đ</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Chưa có chủ nợ.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
