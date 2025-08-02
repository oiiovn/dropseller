@extends('layout')

@section('title', 'Lỗi số dư')

@section('main')
<div class="container mt-4">
    <h4>📛 Danh sách lỗi số dư</h4>
<form action="{{ route('check_AI') }}" method="POST" style="display:inline;">
    @csrf
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-robot"></i> Kiểm tra lỗi số dư
    </button>
</form>
<form action="{{ route('admin.balance_issues.delete_all') }}" method="POST" style="display:inline;">
    @csrf
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-robot"></i> xoá tất cả lỗi
    </button>
</form>
    <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Expected</th>
                <th>Actual</th>
                <th>Lý do GPT</th>
                <th>Thời gian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($issues as $issue)
            <tr>
                <td>{{ $issue->id }}</td>
                <td>{{ $issue->user->name ?? '---' }}</td>
                <td class="text-primary">{{ number_format($issue->expected_balance) }} VND</td>
                <td class="text-danger">{{ number_format($issue->actual_balance) }} VND</td>
                <td>{{ $issue->message }}</td>
                <td>{{ $issue->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">✅ Không có lỗi nào!</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $issues->links() }}
</div>
@endsection
