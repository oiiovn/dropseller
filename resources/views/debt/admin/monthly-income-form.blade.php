@extends('debt.admin.layout')

@section('title', $income ? 'Sửa thu nhập tháng' : 'Nhập thu nhập tháng')

@section('content')
    <h4 class="mb-4">{{ $income ? 'Sửa thu nhập tháng' : 'Nhập thu nhập tháng' }}</h4>
    <div class="debt-card p-4" style="max-width: 400px;">
        <form method="POST" action="{{ $income ? route('debt.admin.monthly-income.update', $income) : route('debt.admin.monthly-income.store') }}">
            @csrf
            @if($income) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Tháng <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                    @for($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ old('month', $income?->month ?? now()->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option> @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Năm <span class="text-danger">*</span></label>
                <input type="number" name="year" class="form-control" required value="{{ old('year', $income?->year ?? now()->year) }}" min="2020" max="2100">
            </div>
            <div class="mb-3">
                <label class="form-label">Số tiền thu nhập (đ) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0" required value="{{ old('amount', $income?->amount) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $income?->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">{{ $income ? 'Cập nhật' : 'Lưu' }}</button>
            <a href="{{ route('debt.admin.monthly-incomes') }}" class="btn btn-outline-secondary">Hủy</a>
        </form>
    </div>
@endsection
