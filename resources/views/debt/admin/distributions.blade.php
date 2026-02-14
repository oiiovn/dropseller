@extends('debt.admin.layout')

@section('title', 'Phân bổ & Thanh toán')

@section('content')
    <h4 class="mb-4">Phân bổ theo tháng – Đánh dấu đã thanh toán</h4>
    <div class="debt-card overflow-hidden">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width: 50px;"></th><th>Tháng/Năm</th><th>Tổng phân bổ</th><th>Đã thanh toán / Chờ</th></tr></thead>
            <tbody>
                @forelse($monthlyIncomes as $income)
                    @php
                        $dists = $income->distributions;
                        $paidCount = $dists->where('status', 'paid')->count();
                        $pendingCount = $dists->where('status', 'pending')->count();
                        $totalAmount = $dists->sum('amount');
                    @endphp
                    <tr class="align-middle" data-bs-toggle="collapse" data-bs-target="#month-{{ $income->id }}" role="button" aria-expanded="false" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="bi bi-chevron-down collapse-icon"></i>
                        </td>
                        <td><strong>{{ $income->month }}/{{ $income->year }}</strong></td>
                        <td>{{ number_format($totalAmount, 0, ',', '.') }} đ</td>
                        <td><span class="text-success">{{ $paidCount }}</span> / <span class="text-warning">{{ $pendingCount }}</span> chủ nợ</td>
                    </tr>
                    <tr class="collapse" id="month-{{ $income->id }}">
                        <td colspan="4" class="p-0 border-0 bg-light">
                            <div class="p-3">
                                <table class="table table-sm table-bordered mb-0 bg-white">
                                    <thead><tr><th>Chủ nợ</th><th>Số tiền</th><th>Mã giao dịch</th><th>Trạng thái</th><th>Mã thanh toán NH</th><th>Ngày thanh toán</th><th>Thao tác</th></tr></thead>
                                    <tbody>
                                        @foreach($income->distributions as $d)
                                            <tr>
                                                <td>{{ $d->debtCreditor->user->name }}</td>
                                                <td>
                                                    @if($d->status !== 'paid')
                                                        <input type="number" name="amount" form="form-paid-{{ $d->id }}" class="form-control form-control-sm d-inline-block" value="{{ $d->amount }}" min="0" step="1000" style="width: 120px;"> đ
                                                    @else
                                                        {{ number_format($d->amount, 0, ',', '.') }} đ
                                                    @endif
                                                </td>
                                                <td><code>{{ $d->transaction_code }}</code></td>
                                                <td><span class="badge {{ $d->status === 'paid' ? 'badge-paid' : 'badge-pending' }}">{{ $d->status === 'paid' ? 'Đã thanh toán' : 'Chờ' }}</span></td>
                                                <td><small>{{ $d->bank_transaction_ref ?: '—' }}</small></td>
                                                <td><small>{{ $d->paid_at ? $d->paid_at->format('d/m/Y H:i') : '—' }}</small></td>
                                                <td>
                                                    @if($d->status !== 'paid')
                                                        <form id="form-paid-{{ $d->id }}" method="POST" action="{{ route('debt.admin.mark-paid', $d) }}" class="d-inline">
                                                            @csrf
                                                            <input type="text" name="bank_transaction_ref" class="form-control form-control-sm d-inline-block w-auto me-1" placeholder="Mã CK" style="width: 110px;">
                                                            <button type="submit" class="btn btn-sm btn-success">Đã chuyển</button>
                                                        </form>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Chưa có phân bổ. Nhập thu nhập tháng rồi bấm Phân bổ.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-2">{{ $monthlyIncomes->links() }}</div>
    </div>
    <style>
        [data-bs-toggle="collapse"] .collapse-icon { transition: transform .2s; }
        [data-bs-toggle="collapse"][aria-expanded="true"] .collapse-icon { transform: rotate(180deg); }
    </style>
    @if (session('expand_income_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var id = {{ (int) session('expand_income_id') }};
            var el = document.getElementById('month-' + id);
            if (el) {
                var c = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                c.show();
                var trigger = document.querySelector('[data-bs-target="#month-' + id + '"]');
                if (trigger) trigger.setAttribute('aria-expanded', 'true');
            }
        });
    </script>
    @endif
@endsection
