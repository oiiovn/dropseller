@extends('debt.admin.layout')

@section('title', 'Lịch sử truy cập chủ nợ')

@section('content')
    <h4 class="mb-4">Lịch sử truy cập chủ nợ</h4>
    <p class="text-muted small mb-4">Đăng nhập và các trang chủ nợ đã xem. Chỉ dùng nội bộ, chủ nợ không thấy trang này.</p>

    <div class="debt-card mb-4">
        <h6 class="p-3 border-bottom mb-0">Số lần đăng nhập theo chủ nợ</h6>
        <table class="table table-hover mb-0">
            <thead><tr><th>Chủ nợ</th><th>Email</th><th>Đăng nhập (tất cả)</th><th>Đăng nhập (7 ngày qua)</th></tr></thead>
            <tbody>
                @forelse($creditors as $c)
                    <tr>
                        <td>{{ $c->user->name }}</td>
                        <td>{{ $c->user->email }}</td>
                        <td>{{ $loginCounts[$c->user_id] ?? 0 }} lần</td>
                        <td>{{ $loginCountsLastWeek[$c->user_id] ?? 0 }} lần</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Chưa có chủ nợ.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="GET" action="{{ route('debt.admin.activity-logs') }}" class="row g-2 mb-3">
        <div class="col-auto">
            <label class="form-label visually-hidden">Chủ nợ</label>
            <select name="user_id" class="form-select form-select-sm">
                <option value="">Tất cả chủ nợ</option>
                @foreach($creditors as $c)
                    <option value="{{ $c->user_id }}" {{ request('user_id') == $c->user_id ? 'selected' : '' }}>{{ $c->user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label visually-hidden">Hành động</label>
            <select name="action" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Đăng nhập</option>
                <option value="login_failed" {{ request('action') === 'login_failed' ? 'selected' : '' }}>Đăng nhập thất bại</option>
                <option value="view_page" {{ request('action') === 'view_page' ? 'selected' : '' }}>Xem trang</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label visually-hidden">Từ ngày</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="Từ ngày">
        </div>
        <div class="col-auto">
            <label class="form-label visually-hidden">Đến ngày</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="Đến ngày">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
        </div>
    </form>

    <div class="debt-card overflow-hidden">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Chủ nợ</th>
                    <th>Hành động</th>
                    <th>Trang / Route</th>
                    <th>IP</th>
                    <th>Thiết bị</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->user->name ?? '—' }}<br><small class="text-muted">{{ $log->user->email ?? '' }}</small></td>
                        <td>
                            @if($log->action === 'login')
                                <span class="badge bg-success">Đăng nhập</span>
                            @elseif($log->action === 'login_failed')
                                <span class="badge bg-danger">Đăng nhập thất bại</span>
                            @else
                                <span class="badge bg-secondary">Xem trang</span>
                            @endif
                        </td>
                        <td><code class="small">{{ $log->route_name ?: $log->path ?: '—' }}</code></td>
                        <td><code class="small">{{ $log->ip_address }}</code></td>
                        <td class="small text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->user_agent }}">{{ $log->user_agent ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Chưa có bản ghi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-2">{{ $logs->links() }}</div>
    </div>
@endsection
