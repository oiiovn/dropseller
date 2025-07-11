@extends('layout')
@section('title', 'Tra cứu số điện thoại khách hàng')

@section('main')

<div class="container mt-5">
    <h3 class="mb-4">📨 Gửi username khách hàng</h3>

    {{-- Form nhập username --}}
    <form method="POST" action="{{ route('username.submit') }}" class="row g-3 mb-5">
        @csrf
        <div class="col-md-6 col-sm-12">
            <input type="text" name="username" class="form-control" placeholder="Nhập username" required value="{{ old('username') }}">
            @error('username')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-2 col-sm-12">
            <button type="submit" class="btn btn-primary w-100">Gửi đi</button>
        </div>
    </form>

    {{-- Danh sách kết quả --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            📋 Danh sách đã gửi
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%">STT</th>
                            <th>Username</th>
                            <th>Số điện thoại</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $index => $record)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $record->username }}</td>
                            <td class="text-center">
                                @if ($record->status === 'pending')
                                <span class="text-warning">[ . . . ]</span>
                                @elseif (preg_match('/\d{8,15}/', $record->status))
                                <span class="text-success">{{ $record->status }}</span>
                                @else
                                <span class="text-danger">Không check được (Chờ hoàn)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($record->status === 'pending')
                                <span class="badge bg-warning text-dark">Đang check</span>
                                @elseif (preg_match('/\d{8,15}/', $record->status))
                                <span class="badge bg-success">Thành công</span>
                                @else
                                <span class="badge bg-danger">Thất bại</span>
                                @endif
                            </td>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection