@extends('debt.admin.layout')

@section('title', 'Lịch sử ngân hàng Pay2s')

@section('content')
    <h4 class="mb-4">Lịch sử ngân hàng</h4>
    <div class="debt-card p-4 mb-4">
        <form method="GET" action="{{ route('debt.admin.bank-history-pay2s') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="begin" class="form-control" value="{{ $begin_ymd }}" title="Chuẩn giờ HCM (GMT+7)">
            </div>
            <div class="col-auto">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="end" class="form-control" value="{{ $end_ymd }}" title="Chuẩn giờ HCM (GMT+7)">
            </div>
            <div class="col-auto">
                <label class="form-label">Số tài khoản (nhiều TK cách nhau bằng dấu phẩy)</label>
                <input type="text" name="bank_accounts" class="form-control" style="min-width: 280px;" value="{{ $bank_accounts }}" placeholder="VD: 008338298888, 46241987">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Xem / Lọc</button>
            </div>
        </form>
    </div>

    <!-- Dashboard SHOPEEPAY & Grab -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="debt-card p-3 border-danger border-opacity-25">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar-title rounded fs-4 bg-danger bg-opacity-10 text-danger"><i class="bi bi-wallet2"></i></span>
                    <span class="ms-2 fw-semibold">ShopeeFood</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">Vào:</span><span>{{ number_format($shopeepay_in ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Ra:</span><span>{{ number_format($shopeepay_out ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Số GD:</span><span>{{ $shopeepay_count ?? 0 }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="debt-card p-3 border-success border-opacity-25">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar-title rounded fs-4 bg-success bg-opacity-10 text-success"><i class="bi bi-car-front"></i></span>
                    <span class="ms-2 fw-semibold">GrabFood</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">Vào:</span><span>{{ number_format($grab_in ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Ra:</span><span>{{ number_format($grab_out ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Số GD:</span><span>{{ $grab_count ?? 0 }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="debt-card p-3 border-warning border-opacity-25">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar-title rounded fs-4 bg-warning bg-opacity-10 text-warning"><i class="bi bi-cup-hot"></i></span>
                    <span class="ms-2 fw-semibold">CAFE</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">Vào:</span><span>{{ number_format($cafe_in ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Ra:</span><span>{{ number_format($cafe_out ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Số GD:</span><span>{{ $cafe_count ?? 0 }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="debt-card p-3 border-info border-opacity-25">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar-title rounded fs-4 bg-info bg-opacity-10 text-info"><i class="bi bi-currency-exchange"></i></span>
                    <span class="ms-2 fw-semibold">Vốn Food</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">Vào:</span><span>{{ number_format($von_food_in ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Ra:</span><span>{{ number_format($von_food_out ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Số GD:</span><span>{{ $von_food_count ?? 0 }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="debt-card p-3 border-secondary border-opacity-25">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar-title rounded fs-4 bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-upc-scan"></i></span>
                    <span class="ms-2 fw-semibold">BEFood</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">Vào:</span><span>{{ number_format($be_in ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Ra:</span><span>{{ number_format($be_out ?? 0, 0, ',', '.') }} đ</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Số GD:</span><span>{{ $be_count ?? 0 }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="debt-card overflow-hidden">
        <p class="p-3 mb-0 small text-muted">Tổng <strong>{{ $transactions->total() }}</strong> giao dịch (lấy từ bảng trong DB).</p>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Ngày GD</th>
                        <th>Mã GD</th>
                        <th>Ngân hàng</th>
                        <th>Số TK</th>
                        <th>Loại</th>
                        <th class="text-end">Số tiền</th>
                        <th>Nội dung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td><small>{{ $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y H:i') : '—' }}</small></td>
                            <td><code class="small">{{ $t->transaction_id ?? '—' }}</code></td>
                            <td>{{ $t->bank ?? '—' }}</td>
                            <td>{{ $t->account_number ?? '—' }}</td>
                            <td><span class="badge {{ $t->type === 'IN' ? 'bg-success' : 'bg-secondary' }}">{{ $t->type ?? '—' }}</span></td>
                            <td class="text-end">{{ $t->amount !== null ? number_format((float)$t->amount, 0, ',', '.') . ' đ' : '—' }}</td>
                            <td><small class="text-break">{{ $t->description ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4">Chưa có giao dịch trong khoảng đã chọn. Đồng bộ từ Pay2s bằng lệnh <code>php artisan fetch:transactions</code>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2">{{ $transactions->links() }}</div>
    </div>
@endsection
