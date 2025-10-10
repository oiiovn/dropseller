@extends('layout')
@section('title', 'Quản lý đơn hoàn')

@section('main')
<div class="container-fluid bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Quản lý đơn hoàn - Tất cả User</h4>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payAllModal">
            <i class="ri-money-dollar-circle-line"></i> Thanh toán tất cả đơn hoàn
        </button>
    </div>

    <!-- Modal thanh toán tất cả -->
    <div class="modal fade" id="payAllModal" tabindex="-1" aria-labelledby="payAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('return-orders.pay-all') }}" method="POST" onsubmit="return confirm('Xác nhận thanh toán TẤT CẢ đơn hoàn chưa thanh toán?')">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="payAllModalLabel">
                            <i class="ri-money-dollar-circle-line"></i> Thanh toán tất cả đơn hoàn
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ri-alert-line"></i>
                            <strong>Cảnh báo:</strong> Hành động này sẽ thanh toán TẤT CẢ đơn hoàn có trạng thái "Chưa thanh toán"!
                        </div>
                        <div class="alert alert-info">
                            <i class="ri-information-line"></i>
                            <strong>Hệ thống sẽ:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tìm tất cả đơn hoàn "Chưa thanh toán"</li>
                                <li>Tạo giao dịch cho từng đơn (bank: DROP, type: IN)</li>
                                <li>Cập nhật trạng thái thành "Đã thanh toán"</li>
                                <li>Hiển thị kết quả chi tiết sau khi hoàn tất</li>
                            </ul>
                        </div>
                        <p class="mb-0 text-center">
                            <strong>Bạn có chắc chắn muốn tiếp tục?</strong>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ri-close-line"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-check-line"></i> Xác nhận thanh toán tất cả
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('return-orders.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chưa thanh toán" {{ request('status') == 'Chưa thanh toán' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="Đã thanh toán" {{ request('status') == 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Shop</label>
                    <select name="shop_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->shop_id }}" {{ request('shop_id') == $shop->shop_id ? 'selected' : '' }}>
                                {{ $shop->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line"></i> Lọc
                    </button>
                    <a href="{{ route('return-orders.index') }}" class="btn btn-secondary">
                        <i class="ri-refresh-line"></i> Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Tổng đơn hoàn</h6>
                    <h4>{{ $returnOrders->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Chưa thanh toán</h6>
                    <h4>{{ $returnOrders->where('payment_status', 'Chưa thanh toán')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Đã thanh toán</h6>
                    <h4>{{ $returnOrders->where('payment_status', 'Đã thanh toán')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Tổng tiền</h6>
                    <h4>{{ number_format($returnOrders->sum('tong_tien')) }}đ</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã đơn</th>
                    <th>Ngày</th>
                    <th>User</th>
                    <th>Shop</th>
                    <th>SKU</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Transaction ID</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returnOrders as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        <a href="#" class="text-primary">{{ $item->order_code }}</a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->ngay)->format('d/m/Y') }}</td>
                    <td>
                        @if($item->shop && $item->shop->user)
                            <div>
                                <strong>{{ $item->shop->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->shop->user->referral_code }}</small>
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($item->shop)
                            <div>{{ $item->shop->shop_name }}</div>
                            <small class="text-muted">{{ $item->shop_id }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if(is_array($item->sku))
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#skuModal{{ $item->id }}">
                                Xem SKU ({{ count($item->sku) }})
                            </button>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="skuModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Danh sách SKU - {{ $item->order_code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                @foreach($item->sku as $sku)
                                                    <li class="list-group-item">{{ $sku }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-danger">{{ number_format($item->tong_tien) }}đ</strong>
                    </td>
                    <td>
                        @if($item->payment_status == 'Đã thanh toán')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning">Chưa thanh toán</span>
                        @endif
                    </td>
                    <td>
                        @if($item->transaction_id)
                            <small class="text-muted">{{ $item->transaction_id }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($item->payment_status == 'Chưa thanh toán')
                            <form method="POST" action="{{ route('return-orders.pay', $item->id) }}" 
                                  onsubmit="return confirm('Xác nhận thanh toán đơn hoàn {{ $item->order_code }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ri-money-dollar-circle-line"></i> Thanh toán
                                </button>
                            </form>
                        @else
                            <span class="text-success">
                                <i class="ri-checkbox-circle-fill"></i> Đã xử lý
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        Không có đơn hoàn nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $returnOrders->withQueryString()->links() }}
    </div>
</div>
@endsection

