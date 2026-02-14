@extends('debt.layout')

@section('sidebar')
    <a href="{{ route('debt.admin.summary') }}" class="{{ request()->routeIs('debt.admin.summary') ? 'active' : '' }}"><i class="bi bi-pie-chart me-2"></i>Tổng quan nợ</a>
    <a href="{{ route('debt.admin.index') }}" class="{{ request()->routeIs('debt.admin.index') ? 'active' : '' }}"><i class="bi bi-people me-2"></i>Chủ nợ</a>
    <a href="{{ route('debt.admin.monthly-incomes') }}" class="{{ request()->routeIs('debt.admin.monthly-incomes*') ? 'active' : '' }}"><i class="bi bi-cash-stack me-2"></i>Thu nhập tháng</a>
    <a href="{{ route('debt.admin.distributions') }}" class="{{ request()->routeIs('debt.admin.distributions') ? 'active' : '' }}"><i class="bi bi-list-check me-2"></i>Phân bổ & Thanh toán</a>
    <a href="{{ route('debt.admin.payment-history') }}" class="{{ request()->routeIs('debt.admin.payment-history') ? 'active' : '' }}"><i class="bi bi-clock-history me-2"></i>Lịch sử thanh toán nợ</a>
    <a href="{{ route('debt.admin.bank-history-pay2s') }}" class="{{ request()->routeIs('debt.admin.bank-history-pay2s*') ? 'active' : '' }}"><i class="bi bi-bank me-2"></i>Lịch sử NH Pay2s</a>
    <a href="{{ route('dashboard') }}" class=""><i class="bi bi-box-arrow-left me-2"></i>Về hệ thống chính</a>
    <form method="POST" action="{{ route('logout') }}" class="p-2 mt-2">@csrf<button type="submit" class="btn btn-outline-light btn-sm w-100">Đăng xuất</button></form>
@endsection
