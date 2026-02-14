@extends('debt.admin.layout')

@section('title', 'Thu nhập tháng')

@section('content')
    <h4 class="mb-4">Thu nhập theo tháng</h4>
    <div class="mb-3">
        <a href="{{ route('debt.admin.monthly-income.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>Nhập thu nhập tháng</a>
        <a href="{{ route('debt.admin.monthly-income.create-bulk-60') }}" class="btn btn-outline-primary ms-2"><i class="bi bi-calendar-range me-1"></i>Tạo 60 tháng tiếp theo</a>
    </div>
    <form method="POST" action="{{ route('debt.admin.monthly-income.distribute-bulk') }}" id="form-bulk-distribute">
        @csrf
        <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="select-all">Chọn tất cả trang</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselect-all">Bỏ chọn</button>
            <button type="submit" class="btn btn-primary btn-sm">Phân bổ hàng loạt (đã chọn)</button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="btn-delete-bulk">Xóa phân bổ hàng loạt (đã chọn)</button>
        </div>
        <form id="form-delete-bulk" method="POST" action="{{ route('debt.admin.monthly-income.delete-distributions-bulk') }}" class="d-none">@csrf</form>
        <div class="debt-card overflow-hidden">
            <table class="table table-hover mb-0">
                <thead><tr><th style="width: 40px;"><input type="checkbox" id="check-all" class="form-check-input" title="Chọn tất cả"></th><th>Tháng/Năm</th><th>Số tiền</th><th>Phân bổ</th><th>Thao tác</th></tr></thead>
                <tbody>
                    @forelse($incomes as $i)
                        <tr>
                            <td><input type="checkbox" name="income_ids[]" value="{{ $i->id }}" class="form-check-input row-check"></td>
                            <td>{{ $i->month }}/{{ $i->year }}</td>
                            <td>{{ number_format($i->amount, 0, ',', '.') }} đ</td>
                            <td>{{ $i->distributions->count() }} chủ nợ</td>
                            <td>
                                <a href="{{ route('debt.admin.monthly-income.edit', $i) }}" class="btn btn-sm btn-outline-secondary me-1">Sửa</a>
                                @if($i->distributions->count() > 0)
                                    <form id="delete-dist-{{ $i->id }}" method="POST" action="{{ route('debt.admin.monthly-income.delete-distributions', $i) }}" class="d-inline" onsubmit="return confirm('Xóa toàn bộ phân bổ tháng {{ $i->month }}/{{ $i->year }}? Sau đó có thể bấm Phân bổ để tạo lại.');">@csrf<button type="submit" class="btn btn-sm btn-outline-danger me-1">Xóa phân bổ</button></form>
                                @endif
                                <a href="{{ route('debt.admin.monthly-income.distribute', $i) }}" class="btn btn-sm btn-primary" onclick="event.preventDefault(); document.getElementById('single-distribute-{{ $i->id }}').submit();">{{ $i->distributions->count() > 0 ? 'Phân bổ lại' : 'Phân bổ' }}</a>
                                <form id="single-distribute-{{ $i->id }}" method="POST" action="{{ route('debt.admin.monthly-income.distribute', $i) }}" class="d-none">@csrf</form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Chưa có thu nhập. Nhập thu nhập tháng rồi bấm "Phân bổ" để tạo kế hoạch trả theo tỉ lệ từng chủ nợ.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-2">{{ $incomes->links() }}</div>
        </div>
    </form>
    <script>
        document.getElementById('select-all')?.addEventListener('click', function() {
            document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = true; });
            document.getElementById('check-all').checked = true;
        });
        document.getElementById('deselect-all')?.addEventListener('click', function() {
            document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = false; });
            document.getElementById('check-all').checked = false;
        });
        document.getElementById('check-all')?.addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = this.checked; }.bind(this));
        });
        document.getElementById('form-bulk-distribute')?.addEventListener('submit', function(e) {
            var n = document.querySelectorAll('.row-check:checked').length;
            if (n === 0) { e.preventDefault(); alert('Vui lòng chọn ít nhất một tháng để phân bổ.'); }
        });
        document.getElementById('btn-delete-bulk')?.addEventListener('click', function() {
            var checked = document.querySelectorAll('.row-check:checked');
            if (checked.length === 0) { alert('Vui lòng chọn ít nhất một tháng để xóa phân bổ.'); return; }
            if (!confirm('Xóa toàn bộ phân bổ của ' + checked.length + ' tháng đã chọn? Sau đó có thể bấm Phân bổ hàng loạt để tạo lại.')) return;
            var form = document.getElementById('form-delete-bulk');
            form.innerHTML = '';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = document.querySelector('input[name="_token"]').value;
            form.appendChild(input);
            checked.forEach(function(cb) {
                var i = document.createElement('input');
                i.type = 'hidden';
                i.name = 'income_ids[]';
                i.value = cb.value;
                form.appendChild(i);
            });
            form.submit();
        });
    </script>
@endsection
