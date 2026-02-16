@extends('debt.layout')

@section('title', 'Tổng quan | Quản lý nợ')

@section('sidebar')
    <a href="{{ route('debt.dashboard') }}" class="{{ request()->routeIs('debt.dashboard') ? 'active' : '' }}"><i class="bi bi-house me-2"></i>Trang chủ</a>
    <a href="{{ route('debt.notice-restructuring') }}" class="{{ request()->routeIs('debt.notice-restructuring') ? 'active' : '' }}"><i class="bi bi-file-text me-2"></i>Thông báo tái cấu trúc</a>
    <a href="{{ route('debt.account') }}" class="{{ request()->routeIs('debt.account*') ? 'active' : '' }}"><i class="bi bi-person me-2"></i>Email & mật khẩu</a>
    <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf<button type="submit" class="btn btn-outline-light btn-sm w-100">Đăng xuất</button></form>
@endsection

@section('content')
    <h4 class="mb-4"> {{ Auth::user()->name }}</h4>
    @if($creditor)
        <div class="debt-card p-4 mb-4">
            <h5>Tổng nợ bạn</h5>
            <p class="fs-4 mb-0"><a href="{{ route('debt.old-debt.detail') }}" class="text-primary text-decoration-none">{{ number_format($creditor->total_debt, 0, ',', '.') }} đ</a></p>
            <hr class="my-3">
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Đã thanh toán</span>
                    <strong class="text-success">{{ number_format($totalPaid, 0, ',', '.') }} đ</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Còn lại</span>
                    <strong>{{ number_format($remaining, 0, ',', '.') }} đ</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Trung bình/ tháng</span>
                    <strong>{{ number_format($averageMonthlyPayment, 0, ',', '.') }} đ</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Dự kiến hoàn tất
                        <span class="debt-tooltip-wrap position-relative d-inline-block ms-1">
                            <i class="bi bi-exclamation-circle text-muted opacity-75 debt-tooltip-btn" style="font-size: 0.85rem; cursor: help;" tabindex="0" role="button" aria-label="Giải thích"></i>
                            <span class="debt-tooltip-box">Thời gian dự kiến rút ngắn dần theo các khoản đã thanh toán (theo lịch phân bổ đã thanh toán), thường giảm khoảng 30 tháng so với ban đầu. Khi một chủ nợ được thanh toán hết, phần tiền đó dồn sang chủ nợ còn lại, đẩy nhanh thời gian hoàn tất.</span>
                        </span>
                    </span>
                    <strong>{{ $estimatedCompletionMonth ? $estimatedCompletionMonth->format('m/Y') : '—' }}</strong>
                </div>
            </div>
            <div class="mt-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Tiến độ thanh toán</span>
                    <span>{{ number_format($progressPercent, 2, '.', '') }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <div class="debt-card p-4">
            <h5>Lịch sử phân bổ / Mã giao dịch</h5>
            <p class="text-muted small">Mỗi tháng bạn nhận một khoản theo tỉ lệ. Dùng mã giao dịch để đối chiếu khi nhận chuyển khoản.</p>
            <form method="GET" action="{{ route('debt.dashboard') }}" class="mb-3 d-flex align-items-center gap-3 flex-wrap">
                @if(!empty($years))
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Năm:</label>
                        <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0">Trạng thái:</label>
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="" {{ $selectedStatus === '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="cho" {{ $selectedStatus === 'cho' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="du_kien" {{ $selectedStatus === 'du_kien' ? 'selected' : '' }}>Dự kiến</option>
                    </select>
                </div>
            </form>
            @if($distributions->isEmpty())
                <p class="text-muted">Chưa có phân bổ nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Tháng/Năm</th><th>Số tiền</th><th>Mã giao dịch</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            @foreach($distributions as $d)
                                @php
                                    $y = (int) $d->debtMonthlyIncome->year;
                                    $m = (int) $d->debtMonthlyIncome->month;
                                    $isFutureMonth = $y > now()->year || ($y === now()->year && $m > now()->month);
                                    $label = $d->status === 'paid' ? 'Đã thanh toán' : ($isFutureMonth ? 'Dự kiến' : 'Chờ thanh toán');
                                    $badgeClass = $d->status === 'paid' ? 'badge-paid' : ($isFutureMonth ? 'bg-info' : 'badge-pending');
                                    $rowClass = $isFutureMonth && $d->status !== 'paid' ? 'table-secondary opacity-75' : '';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>{{ $d->debtMonthlyIncome->month }}/{{ $d->debtMonthlyIncome->year }}</td>
                                    <td>{{ number_format($d->amount, 0, ',', '.') }} đ</td>
                                    <td>
                                        @if($d->status === 'paid')
                                            <code>{{ $d->transaction_code }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $badgeClass }}">{{ $label }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-warning">Bạn chưa được gắn với hồ sơ chủ nợ. Liên hệ quản trị viên.</div>
    @endif
@endsection

@push('styles')
<style>
.debt-tooltip-wrap .debt-tooltip-box {
    position: absolute; left: 50%; transform: translateX(-50%); bottom: calc(100% + 6px);
    width: 280px; max-width: 90vw; padding: 8px 10px; font-size: 0.8rem; line-height: 1.4;
    background: #333; color: #fff; border-radius: 6px; white-space: normal;
    visibility: hidden; opacity: 0; transition: opacity 0.15s, visibility 0.15s;
    z-index: 1050; box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.debt-tooltip-wrap .debt-tooltip-box::after {
    content: ''; position: absolute; top: 100%; left: 50%; margin-left: -5px;
    border: 5px solid transparent; border-top-color: #333;
}
.debt-tooltip-wrap:hover .debt-tooltip-box,
.debt-tooltip-wrap.show .debt-tooltip-box { visibility: visible; opacity: 1; }
</style>
@endpush
@push('scripts')
<script>
(function() {
    var wrap = document.querySelector('.debt-tooltip-wrap');
    if (!wrap) return;
    var btn = wrap.querySelector('.debt-tooltip-btn');
    btn.addEventListener('click', function(e) { e.preventDefault(); wrap.classList.toggle('show'); });
    document.addEventListener('click', function(e) {
        if (!wrap.contains(e.target)) wrap.classList.remove('show');
    });
})();
</script>
@endpush
