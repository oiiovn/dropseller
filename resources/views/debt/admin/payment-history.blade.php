@extends('debt.admin.layout')

@section('title', 'Lịch sử thanh toán nợ')

@section('content')
    <h4 class="mb-4">Lịch sử thanh toán nợ</h4>
    <p class="text-muted small">Các khoản đã thanh toán (ghi nhận thủ công hoặc từ quét giao dịch ngân hàng khi nội dung chứa mã giao dịch).</p>
    <div class="debt-card overflow-hidden">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tháng/Năm</th><th>Chủ nợ</th><th>Số tiền</th><th>Mã giao dịch</th><th>Mã thanh toán ngân hàng</th><th>Ngày thanh toán</th></tr></thead>
            <tbody>
                @forelse($payments as $d)
                    <tr>
                        <td>{{ $d->debtMonthlyIncome->month }}/{{ $d->debtMonthlyIncome->year }}</td>
                        <td>{{ $d->debtCreditor->user->name }}</td>
                        <td>{{ number_format($d->amount, 0, ',', '.') }} đ</td>
                        <td><code>{{ $d->transaction_code }}</code></td>
                        <td><code class="small">{{ $d->bank_transaction_ref ?: '—' }}</code></td>
                        <td>{{ $d->paid_at ? $d->paid_at->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Chưa có thanh toán nào được ghi nhận.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-2">{{ $payments->links() }}</div>
    </div>
@endsection
