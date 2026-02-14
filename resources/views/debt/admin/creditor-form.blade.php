@extends('debt.admin.layout')

@section('title', $creditor ? 'Sửa chủ nợ' : 'Thêm chủ nợ')

@section('content')
    <h4 class="mb-4">{{ $creditor ? 'Sửa chủ nợ' : 'Thêm chủ nợ' }}</h4>
    <div class="debt-card p-4" style="max-width: 560px;">
        <form method="POST" action="{{ $creditor ? route('debt.admin.creditor.update', $creditor) : route('debt.admin.creditor.store') }}">
            @csrf
            @if($creditor) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $creditor?->user->name) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $creditor?->user->email) }}" {{ $creditor ? '' : 'autocomplete=off' }}>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu {{ $creditor ? '(để trống nếu không đổi)' : '' }} <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" {{ $creditor ? '' : 'required' }} autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label class="form-label">Mã số cá nhân (do bạn cấp) <span class="text-danger">*</span></label>
                <input type="text" name="personal_code" class="form-control" required value="{{ old('personal_code', $creditor?->user->personal_code) }}" placeholder="VD: CHUNO001">
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $creditor?->phone) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Tổng nợ (số tiền)</label>
                <input type="number" name="total_debt" class="form-control" step="0.01" min="0" value="{{ old('total_debt', $creditor?->total_debt ?? 0) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Ngày tái cấu trúc</label>
                <input type="date" name="restructuring_date" class="form-control" value="{{ old('restructuring_date', $creditor?->restructuring_date?->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $creditor?->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">{{ $creditor ? 'Cập nhật' : 'Tạo tài khoản chủ nợ' }}</button>
            <a href="{{ route('debt.admin.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </form>
    </div>
@endsection
