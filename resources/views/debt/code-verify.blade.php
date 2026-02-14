@extends('layouts.auth')

@section('title', 'Xác thực mã số | Quản lý nợ')

@section('content')
<div class="layout-wrapper d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow-sm rounded-3" style="max-width: 400px; width: 100%;">
        <div class="card-body p-4">
            <h5 class="card-title mb-3">Nhập mã số cá nhân</h5>
            <p class="text-muted small">Mã do quản trị viên cấp cho bạn.</p>
            <form method="POST" action="{{ route('debt.code.verify.post') }}">
                @csrf
                <div class="mb-3">
                    <label for="personal_code" class="form-label">Mã số cá nhân</label>
                    <input type="text" name="personal_code" id="personal_code" class="form-control form-control-lg" required autofocus placeholder="Nhập mã">
                </div>
                <button type="submit" class="btn btn-primary w-100">Xác nhận</button>
            </form>
            <div class="mt-3 text-center">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link text-muted p-0">Đăng xuất</button></form>
            </div>
        </div>
    </div>
</div>
@endsection
