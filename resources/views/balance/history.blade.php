@extends('layout')

@section('title', 'Biến động số dư')

@section('main')
<div class="container mt-1 p-2 bg-white">
    
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-8 col-lg-8">
            <div class="card search-card shadow-sm p-2">
                <form id="balance-search-form" class="d-flex align-items-center gap-2">
                    <input type="text" name="search" id="search-input" class="form-control form-control-lg search-input flex-grow-1" placeholder="Tìm kiếm mã GD, loại, ghi chú...">
                    <select name="type" id="type-select" class="form-select form-select-lg">
                        <option value="">Tất cả loại</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center gap-1 search-btn">
                        <i class="bi bi-search"></i> <span class="d-none d-md-inline text-nowrap">Tìm kiếm</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="row" id="balance-history-cards">
        @foreach ($histories ?? [] as $item)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card balance-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="balance-icon">
                                @switch($item->type)
                                    @case('deposit') <i class="bi bi-arrow-down-circle-fill text-success"></i> @break
                                    @case('withdraw') <i class="bi bi-arrow-up-circle-fill text-danger"></i> @break
                                    @case('order') <i class="bi bi-bag-check-fill text-warning"></i> @break
                                    @case('refund') <i class="bi bi-arrow-repeat text-info"></i> @break
                                    @case('ads') <i class="bi bi-bullseye text-dark"></i> @break
                                    @case('Monthly') <i class="bi bi-calendar-check text-dark"></i> @break
                                    @case('product_fee') <i class="bi bi-cash-coin text-secondary"></i> @break
                                    @default <i class="bi bi-cash-stack text-secondary"></i>
                                @endswitch
                            </span>
                            <span class="badge balance-type-badge">
                                @switch($item->type)
                                    @case('deposit') Nạp tiền @break
                                    @case('withdraw') Quyết toán @break
                                    @case('order') Đơn hàng @break
                                    @case('refund') Hoàn huỷ @break
                                    @case('ads') Quảng cáo @break
                                    @case('Monthly') Quyết toán @break
                                    @case('product_fee') Phí đăng sản phẩm @break
                                    @default {{ ucfirst($item->type) }}
                                @endswitch
                            </span>
                        </div>
                        <span class="text-muted small">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Mã GD:</span> <span class="fw-semibold text-primary">{{ $item->transaction_code ?? '---' }}</span>
                    </div>
                    <div class="mb-2 fw-bold balance-amount {{ $item->amount_change >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $item->amount_change >= 0 ? '-' : '' }}{{ number_format(abs($item->amount_change)) }} VND
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Số dư sau:</span> <span class="fw-semibold">{{ number_format($item->balance_after) }} VND</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Ghi chú:</span>
                        @if($item->note)
                            <span class="note-box">{{ $item->note }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <nav id="balance-pagination" class="mt-4 d-flex justify-content-center">
        @if(isset($histories) && $histories->hasPages())
            {{ $histories->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
        @endif
    </nav>
</div>

<style>
#balance-history-cards .balance-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    background: #ffffff;
    min-height: 200px;
    overflow: hidden;
}

#balance-history-cards .balance-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
    border-color: #075985;
}

#balance-history-cards .balance-icon {
    font-size: 1.5rem;
    margin-right: 0.5rem;
    display: flex;
    align-items: center;
}

#balance-history-cards .balance-type-badge {
    font-size: 0.85rem;
    padding: 0.3em 0.6em;
    border-radius: 6px;
    background: #f8f9fa;
    color: #495057;
    font-weight: 500;
    border: 1px solid #dee2e6;
}

#balance-history-cards .balance-amount {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#balance-history-cards .note-box {
    background: #f8f9fa;
    color: #495057;
    border-radius: 4px;
    padding: 0.2em 0.5em;
    font-size: 0.9rem;
    display: inline-block;
    border: 1px solid #e9ecef;
}

#balance-history-cards .card-body {
    padding: 1em;
}

@media (max-width: 767px) {
    #balance-history-cards .card-body {
        padding: 0.75rem;
    }
    #balance-history-cards .balance-card {
        min-height: 180px;
    }
}

.search-card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    background: #ffffff;
    margin-bottom: 1rem;
}

.search-input, .form-select {
    border-radius: 6px;
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    background: #ffffff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.search-input:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0.2rem rgba(0,123,255,0.25);
    outline: 0;
}

.search-btn {
    border-radius: 6px;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    background: #007bff;
    border: 1px solid #007bff;
    transition: all 0.15s ease-in-out;
}

.search-btn:hover, .search-btn:focus {
    background: #56cfe1;
    border-color: #0056b3;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
}

.pagination {
    justify-content: center;
    gap: 0.25rem;
}

.pagination .page-item .page-link {
    border-radius: 6px !important;
    margin: 0 0.1rem;
    color: #007bff;
    border: 1px solid #dee2e6;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
    padding: 0.5rem 0.75rem;
}

.pagination .page-item.active .page-link {
    background: #007bff;
    color: #ffffff;
    font-weight: 600;
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.1);
}

.pagination .page-item .page-link:hover {
    background: #e9ecef;
    color: #56cfe1;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background: #ffffff;
    border-color: #dee2e6;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .search-card {
        margin-bottom: 0.5em;
    }
    
    #balance-search-form {
        flex-direction: column;
        gap: 0.5em;
    }
    
    .search-input, .form-select, .search-btn {
        width: 100%;
    }
}
</style>

<script>
// JavaScript đã được di chuyển vào layout.blade.php để tránh mất khi chuyển trang
// Hàm initBalanceHistory() sẽ được gọi tự động khi trang được load
</script>
@endsection