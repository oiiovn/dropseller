@extends('debt.admin.layout')

@section('title', 'Quản lý chủ nợ')

@section('content')
    <h4 class="mb-4">Chủ nợ</h4>
    <div class="mb-3"><a href="{{ route('debt.admin.creditor.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>Thêm chủ nợ</a></div>
    <div class="debt-card overflow-hidden">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Tên</th><th>Email</th><th>Mã số cá nhân</th><th>Tổng nợ</th><th>% kế hoạch</th><th>Ngày tái cấu trúc</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($creditors as $c)
                    @php $plan = $c->repaymentPlans->first(); @endphp
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->user->name }}</td>
                        <td>{{ $c->user->email }}</td>
                        <td><code>{{ $c->user->personal_code }}</code></td>
                        <td>{{ number_format($c->total_debt, 0, ',', '.') }} đ</td>
                        <td>{{ $plan ? number_format((float) $plan->monthly_percent, 1, ',', '') . '%' : '—' }}</td>
                        <td>{{ $c->restructuring_date ? $c->restructuring_date->format('d/m/Y') : '—' }}</td>
                        <td>
                            <a href="{{ route('debt.admin.creditor.edit', $c) }}" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                            <a href="{{ route('debt.admin.repayment-plans', $c) }}" class="btn btn-sm btn-outline-secondary me-1">Kế hoạch trả</a>
                            <form method="POST" action="{{ route('debt.admin.creditor.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Xóa chủ nợ {{ $c->user->name }}? Sẽ xóa luôn phân bổ, kế hoạch trả và tài khoản đăng nhập.');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted">Chưa có chủ nợ. Thêm chủ nợ và cấp mã số cá nhân cho họ.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
