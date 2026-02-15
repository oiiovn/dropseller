@extends('debt.layout')

@section('title', 'Chi tiết nợ cũ')

@section('sidebar')
    <a href="{{ route('debt.dashboard') }}" class="{{ request()->routeIs('debt.dashboard') ? 'active' : '' }}"><i class="bi bi-house me-2"></i>Trang chủ</a>
    <a href="{{ route('debt.notice-restructuring') }}" class="{{ request()->routeIs('debt.notice-restructuring') ? 'active' : '' }}"><i class="bi bi-file-text me-2"></i>Thông báo tái cấu trúc</a>
    <a href="{{ route('debt.account') }}" class="{{ request()->routeIs('debt.account*') ? 'active' : '' }}"><i class="bi bi-person me-2"></i>Email & mật khẩu</a>
    <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf<button type="submit" class="btn btn-outline-light btn-sm w-100">Đăng xuất</button></form>
@endsection

@section('content')
    <h4 class="mb-4">Chi tiết nợ cũ</h4>
    <p class="text-muted small mb-3">Bảng ghi chép các khoản nợ cũ</p>
    <div class="debt-card overflow-hidden">
        <div class="p-4 border-bottom bg-light">
            <strong>Tổng nợ (theo kế hoạch):</strong> {{ number_format($creditor->total_debt, 0, ',', '.') }} đ
        </div>
        @if($items->isEmpty())
            <div class="p-4 text-muted">Chưa có ghi chép nợ cũ.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Số tiền gốc (đ)</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->code ?: '—' }}</td>
                                <td>{{ number_format($item->principal_amount, 0, ',', '.') }}</td>
                                <td>{{ $item->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top small text-muted">
                Tổng {{ $items->count() }} khoản · Tổng gốc: {{ number_format($items->sum('principal_amount'), 0, ',', '.') }} đ
            </div>
        @endif
    </div>
    <div class="mt-3">
        <a href="{{ route('debt.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Về trang chủ</a>
    </div>
@endsection
