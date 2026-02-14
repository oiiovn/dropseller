@extends('debt.layout')

@section('title', 'Email & mật khẩu | Quản lý nợ')

@section('sidebar')
    <a href="{{ route('debt.dashboard') }}" class="{{ request()->routeIs('debt.dashboard') ? 'active' : '' }}"><i class="bi bi-house me-2"></i>Trang chủ</a>
    <a href="{{ route('debt.notice-restructuring') }}" class="{{ request()->routeIs('debt.notice-restructuring') ? 'active' : '' }}"><i class="bi bi-file-text me-2"></i>Thông báo tái cấu trúc</a>
    <a href="{{ route('debt.account') }}" class="{{ request()->routeIs('debt.account*') ? 'active' : '' }}"><i class="bi bi-person me-2"></i>Email & mật khẩu</a>
    <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf<button type="submit" class="btn btn-outline-light btn-sm w-100">Đăng xuất</button></form>
@endsection

@section('content')
    <h4 class="mb-4">Đổi email và mật khẩu</h4>
    <div class="debt-card p-4" style="max-width: 440px;">
        <form method="POST" action="{{ route('debt.account.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <hr class="my-4">
            <p class="text-muted small mb-2">Đổi email (để trống nếu không đổi)</p>
            <div class="mb-3">
                <label class="form-label">Email mới</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" autocomplete="email" placeholder="{{ $user->email }}">
                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận email mới</label>
                <input type="email" name="email_confirmation" class="form-control" value="{{ old('email_confirmation') }}" autocomplete="email">
                @error('email_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <hr class="my-4">
            <p class="text-muted small mb-2">Đổi mật khẩu (để trống nếu không đổi)</p>
            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password" minlength="8">
                <small class="text-muted">Tối thiểu 8 ký tự.</small>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('debt.dashboard') }}" class="btn btn-outline-secondary">Hủy</a>
        </form>
    </div>
@endsection
