@extends('debt.admin.layout')

@section('title', 'Tạo 60 tháng thu nhập tiếp theo')

@section('content')
    <h4 class="mb-4">Tạo 60 tháng thu nhập tiếp theo</h4>
    <p class="text-muted small">Nhập tháng/năm bắt đầu và số tiền; hệ thống tạo 60 bản ghi thu nhập liên tiếp (cùng số tiền). Tháng đã có sẵn sẽ được bỏ qua.</p>
    <div class="debt-card p-4" style="max-width: 420px;">
        <form method="POST" action="{{ route('debt.admin.monthly-income.store-bulk-60') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tháng bắt đầu <span class="text-danger">*</span></label>
                <select name="start_month" class="form-select" required>
                    @for($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ old('start_month', now()->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option> @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Năm bắt đầu <span class="text-danger">*</span></label>
                <input type="number" name="start_year" class="form-control" required value="{{ old('start_year', now()->year) }}" min="2020" max="2100">
            </div>
            <div class="mb-3">
                <label class="form-label">Số tiền thu nhập mỗi tháng (đ) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0" required value="{{ old('amount') }}" placeholder="VD: 6000000">
            </div>
            <div class="mb-3">
                <label class="form-label">Ghi chú (áp dụng cho tất cả)</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Tạo 60 tháng</button>
            <a href="{{ route('debt.admin.monthly-incomes') }}" class="btn btn-outline-secondary">Hủy</a>
        </form>
    </div>
@endsection
