@extends('debt.admin.layout')

@section('title', 'Ghi chép nợ cũ')

@section('content')
    <h4 class="mb-4">Ghi chép nợ cũ</h4>
    <div class="debt-card p-4 mb-4">
        <form method="GET" action="{{ route('debt.admin.old-debt.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0">Chọn chủ nợ</label>
                <select name="creditor_id" class="form-select" style="width: auto; min-width: 220px;" onchange="this.form.submit()">
                    <option value="">-- Chọn chủ nợ --</option>
                    @foreach($creditors as $c)
                        <option value="{{ $c->id }}" {{ (isset($creditor) && $creditor && $creditor->id === $c->id) ? 'selected' : '' }}>{{ $c->user->name }} ({{ $c->user->email }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($creditor)
        <div class="debt-card overflow-hidden">
            <div class="p-4 border-bottom">
                <h5 class="mb-1">Nợ cũ: {{ $creditor->user->name }}</h5>
                <p class="mb-0 text-primary fw-bold" id="old-debt-total">Tổng tiền: {{ number_format($items->sum('principal_amount'), 0, ',', '.') }} đ</p>
            </div>
            <form method="POST" action="{{ route('debt.admin.old-debt.store', $creditor) }}" id="form-old-debt">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="old-debt-table">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Mã</th>
                                <th style="width: 160px;">Số tiền gốc (đ)</th>
                                <th>Ghi chú</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="old-debt-row">
                                    <td><input type="text" name="items[{{ $loop->index }}][code]" class="form-control form-control-sm" value="{{ old('items.'.$loop->index.'.code', $item->code) }}" placeholder="Mã"></td>
                                    <td><input type="number" name="items[{{ $loop->index }}][principal_amount]" class="form-control form-control-sm" step="0.01" min="0" value="{{ old('items.'.$loop->index.'.principal_amount', $item->principal_amount) }}" placeholder="0"></td>
                                    <td><input type="text" name="items[{{ $loop->index }}][notes]" class="form-control form-control-sm" value="{{ old('items.'.$loop->index.'.notes', $item->notes) }}" placeholder="Ghi chú"></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                </tr>
                            @endforeach
                            <tr class="old-debt-row template-row d-none">
                                <td><input type="text" class="form-control form-control-sm" placeholder="Mã" data-name-code="items[__INDEX__][code]"></td>
                                <td><input type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="0" data-name-amount="items[__INDEX__][principal_amount]"></td>
                                <td><input type="text" class="form-control form-control-sm" placeholder="Ghi chú" data-name-notes="items[__INDEX__][notes]"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                <input type="hidden" data-name-id="items[__INDEX__][id]" value="">
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="add-row"><i class="bi bi-plus me-1"></i>Thêm dòng</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Lưu</button>
                </div>
            </form>
        </div>
    @else
        <p class="text-muted">Chọn một chủ nợ ở trên để ghi chép nợ cũ.</p>
    @endif
@endsection

@push('scripts')
@if($creditor)
<script>
(function() {
    var tbody = document.querySelector('#old-debt-table tbody');
    var template = document.querySelector('.template-row');
    if (!tbody || !template) return;
    var count = tbody.querySelectorAll('.old-debt-row:not(.template-row)').length;

    document.getElementById('add-row')?.addEventListener('click', function() {
        var tr = template.cloneNode(true);
        tr.classList.remove('d-none', 'template-row');
        tr.querySelectorAll('[data-name-code]').forEach(function(inp) { inp.name = inp.getAttribute('data-name-code').replace(/__INDEX__/g, count); inp.removeAttribute('data-name-code'); });
        tr.querySelectorAll('[data-name-amount]').forEach(function(inp) { inp.name = inp.getAttribute('data-name-amount').replace(/__INDEX__/g, count); inp.value = ''; inp.removeAttribute('data-name-amount'); });
        tr.querySelectorAll('[data-name-notes]').forEach(function(inp) { inp.name = inp.getAttribute('data-name-notes').replace(/__INDEX__/g, count); inp.value = ''; inp.removeAttribute('data-name-notes'); });
        tr.querySelectorAll('[data-name-id]').forEach(function(inp) { inp.name = inp.getAttribute('data-name-id').replace(/__INDEX__/g, count); inp.value = ''; inp.removeAttribute('data-name-id'); });
        tbody.insertBefore(tr, template);
        count++;
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            var row = e.target.closest('.old-debt-row');
            if (row && !row.classList.contains('template-row')) row.remove();
            updateTotal();
        }
    });

    function updateTotal() {
        var total = 0;
        tbody.querySelectorAll('.old-debt-row:not(.template-row) input[name*="[principal_amount]"]').forEach(function(inp) {
            total += parseFloat(inp.value) || 0;
        });
        var el = document.getElementById('old-debt-total');
        if (el) el.textContent = 'Tổng tiền: ' + total.toLocaleString('vi-VN') + ' đ';
    }
    document.getElementById('form-old-debt')?.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.indexOf('principal_amount') !== -1) updateTotal();
    });
    document.getElementById('add-row')?.addEventListener('click', function() {
        setTimeout(updateTotal, 0);
    });
})();
</script>
@endif
@endpush
