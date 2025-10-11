@extends('layout')
@section('title', 'main')
@section('main')
<div class="container-fluid mb-1 bg-white">
    <!-- Import Data Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <!-- Shop Import Form
                <div class="col-md-6">
                    <div class="border rounded p-3 mb-3">
                        <h5 class="card-title mb-3">Nhập Dữ Liệu Shop từ Excel</h5>
                        <form action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="shop_file" class="form-label">Chọn file Excel:</label>
                                <input type="file" class="form-control" name="file" id="shop_file" accept=".xlsx,.xls,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Nhập Dữ Liệu Shop</button>
                        </form>
                    </div>
                </div> -->

                <!-- Product Import Form -->
                <div class="col-md-6">
                    <div class="border rounded p-3">
                        <h5 class="card-title mb-3">Cập nhật giá vốn sản phẩm từ salework</h5>
                        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="product_file" class="form-label">Chọn file Excel:</label>
                                <input type="file" class="form-control" name="file" id="product_file" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Nhập Dữ Liệu Sản Phẩm</button>
                        </form>
                    </div>
                </div>

                <!-- Product Sync from API -->
                <div class="col-md-6">
                    <div class="border rounded p-3">
                        <h5 class="card-title mb-3">Đồng bộ giá vốn tự động</h5>
                        <p class="text-muted small mb-3">Lấy giá vốn trực tiếp từ API Salework mà không cần upload file</p>
                        <form action="{{ route('products.sync') }}" method="POST" id="syncForm">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" id="syncBtn">
                                <i class="ri-refresh-line me-1"></i>
                                Đồng bộ giá vốn từ Salework
                            </button>
                        </form>
                        <div id="syncProgress" class="mt-2" style="display:none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                            </div>
                            <small class="text-muted">Đang đồng bộ dữ liệu...</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Messages -->
            <div class="row mt-3">
                <div class="col-12">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('updated'))
                        <div class="alert alert-info">
                            <h6>SKU được cập nhật giá:</h6>
                            <ul class="mb-0">
                                @foreach (session('updated') as $sku)
                                    <li>{{ $sku }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('inserted'))
                        <div class="alert alert-success">
                            <h6>SKU được thêm mới:</h6>
                            <ul class="mb-0">
                                @foreach (session('inserted') as $sku)
                                    <li>{{ $sku }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('skipped'))
                        <div class="alert alert-warning">
                            <h6>SKU bị bỏ qua (trùng giá):</h6>
                            <ul class="mb-0">
                                @foreach (session('skipped') as $sku)
                                    <li>{{ $sku }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <!-- Filter Form -->
                <div class="col-md-8">
                    <form action="{{ route('product.report') }}" method="post" class="d-flex gap-2 align-items-center">
                        @csrf
                        <select name="platform" class="form-select" style="width: auto;">
                            <option value="Tiktok">Tiktok</option>
                            <option value="Shopee">Shopee</option>
                            <option value="Lazada">Lazada</option>
                        </select>
                        <select name="shop_id" id="shop_id" class="form-select" style="width: auto;">
                            <option value="">Chọn Shop</option>
                            @if (!empty($shop_get) && count($shop_get) > 0)
                                @foreach($shop_get as $shop)
                                    <option value="{{ $shop['shop_id'] ?? $shop->shop_id }}"
                                        {{ (isset($shopId) && $shopId == ($shop['shop_id'] ?? $shop->shop_id)) ? 'selected' : '' }}>
                                        {{ $shop['shop_name'] ?? $shop->shop_name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Không có shop nào</option>
                            @endif
                        </select>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}" required>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}" required>
                        <button type="submit" class="btn btn-secondary w-25">Lọc dữ liệu</button>
                    </form>
                </div>

                <!-- Export Button -->
                <div class="col-md-3 text-end">
                    @if (!empty($filteredProducts) && count($filteredProducts) > 0 && !empty($filterDate) && !empty($shopId))
                        <form action="{{ route('order.im') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data" value="{{ json_encode($filteredProducts) }}">
                            <input type="hidden" name="filterDate" value="{{ $filterDate }}">
                            <input type="hidden" name="shop_id" value="{{ $shopId }}">
                            <button type="submit" class="btn btn-primary">Xuất hóa đơn</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Shop ID Filter -->
            <div class="row mt-3">
                <div class="col-md-8">
                    <form action="{{ route('get_shop') }}" method="post" class="d-flex gap-2 align-items-center">
                        @csrf
                        <select name="platform" class="form-select" style="width: auto;">
                            <option value="Tiktok">Tiktok</option>
                            <option value="Shopee">Shopee</option>
                            <option value="Lazada">Lazada</option>
                        </select>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}" required>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}" required>
                        <button type="submit" class="btn btn-secondary w-25">Lọc Shop ID</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    @if (!empty($filteredProducts))
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 750px;">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Mã sản phẩm</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Lượt bán ({{$totalAmounts}})</th>
                                <th scope="col">Giá sỉ</th>
                                <th scope="col">Tổng giá</th>
                                <th scope="col" style="width: 80px;">Hình ảnh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filteredProducts as $product)
                                <tr>
                                    <td>{{ $product['code'] ?? 'Không rõ' }}</td>
                                    <td>{{ $product['name'] ?? 'Không rõ' }}</td>
                                    <td class="text-center">{{ $product['amount'] ?? 0 }}</td>
                                    <td>{{ number_format($product['db_price'] ?? 0) }} VNĐ</td>
                                    <td>{{ number_format($product['db_price'] * $product['amount'] ) }} VNĐ</td>
                                    <td>
                                        @if (!empty($product['image']))
                                            <img src="{{ $product['image'] }}" alt="Hình ảnh" class="img-thumbnail" style="width: 50px;">
                                        @else
                                            <span class="text-muted">Không có hình</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(!empty($Shop_id))
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 750px;">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID SHOP</th>
                                <th scope="col">Tên Shop</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Shop_id as $shop_id)
                                <tr>
                                    <td>
                                        <div class="hienthicopy">
                                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$shop_id}}">
                                                {{ $shop_id }}
                                                <i class="ri-checkbox-multiple-blank-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $shopName = 'Shop Mới';
                                            if (!empty($shop_get) && count($shop_get) > 0) {
                                                foreach ($shop_get as $shop) {
                                                    if ($shop['shop_id'] == $shop_id) {
                                                        $shopName = $shop['shop_name'];
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ $shopName }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                Không tìm thấy nội dung nào phù hợp với bộ lọc.
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const syncForm = document.getElementById('syncForm');
    const syncBtn = document.getElementById('syncBtn');
    const syncProgress = document.getElementById('syncProgress');

    if (syncForm) {
        syncForm.addEventListener('submit', function(e) {
            // Show progress indicator
            syncBtn.disabled = true;
            syncProgress.style.display = 'block';
        });
    }
});
</script>
@endsection