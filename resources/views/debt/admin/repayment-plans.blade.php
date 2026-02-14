@extends('debt.admin.layout')

@section('title', 'Kế hoạch trả - ' . $creditor->user->name)

@section('content')
    <h4 class="mb-4">Kế hoạch trả: {{ $creditor->user->name }}</h4>
    <div class="debt-card p-4 mb-4" style="max-width: 480px;">
        <h6>Kế hoạch trả (áp dụng cho toàn bộ các tháng)</h6>
        <p class="text-muted small mb-3">Chỉ cần % mỗi tháng và ngày nhận; áp dụng cho mọi tháng của chủ nợ này.</p>
        <form method="POST" action="{{ route('debt.admin.repayment-plan.store', $creditor) }}">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-4">
                    <label class="form-label small">% mỗi tháng</label>
                    <input type="number" name="monthly_percent" class="form-control" step="0.01" min="0" max="100" required placeholder="VD: 30">
                </div>
                <div class="col-4">
                    <label class="form-label small">Ngày nhận (1-31)</label>
                    <input type="number" name="pay_day_of_month" class="form-control" min="1" max="31" required value="1">
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary w-100">Lưu</button>
                </div>
            </div>
        </form>
    </div>
    <div class="debt-card overflow-hidden">
        <table class="table table-hover mb-0">
            <thead><tr><th>% tháng</th><th>Ngày nhận hàng tháng</th><th>Trạng thái</th></tr></thead>
            <tbody>
                @forelse($plans as $p)
                    <tr>
                        <td>{{ number_format($p->monthly_percent, 1) }}%</td>
                        <td>Ngày {{ $p->pay_day_of_month }}</td>
                        <td><span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $p->is_active ? 'Đang dùng' : 'Cũ' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Chưa có kế hoạch. Nhập % và ngày nhận rồi bấm Lưu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3"><a href="{{ route('debt.admin.index') }}" class="btn btn-outline-secondary">Quay lại danh sách chủ nợ</a></div>
@endsection
