@extends('layout')
@section('title', 'main')

@section('main')

<div class="container mt-5">
    <h3>📨 Gửi username khách hàng</h3>

    {{-- Form nhập username --}}
    <form method="POST" action="{{ route('username.submit') }}" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="username" class="form-control" placeholder="Nhập username" required value="{{ old('username') }}">
                @error('username')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Gửi đi</button>
            </div>
        </div>
    </form>

    {{-- Danh sách đã gửi --}}
    <div class="card">
        <div class="card-header">
            Danh sách đã gửi
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Username</th>
                        <th>Số điện thoại</th> {{-- Thêm dòng này --}}
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->username }}</td>
                        <td>{{ $record->phone ?? '—' }}</td> {{-- Hiển thị số điện thoại --}}
                        <td>
                            @if ($record->status === 'success')
                            <span class="badge bg-success">Thành công</span>
                            @elseif ($record->status === 'pending')
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                            @else
                            <span class="badge bg-danger">Thất bại</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection