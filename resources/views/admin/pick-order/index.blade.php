@extends('layout')
@section('title', 'Quản Lý Nhặt Hàng')

@section('main')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản Lý Nhặt Hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Nhặt Hàng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'pick-order' ? 'active' : '' }}" 
                               data-bs-toggle="tab" 
                               href="#pick-order" 
                               role="tab" 
                               aria-selected="{{ $activeTab === 'pick-order' ? 'true' : 'false' }}">
                                <i class="ri-archive-line align-middle me-1"></i>
                                <span class="d-none d-sm-inline">Nhặt Hàng</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'purchase-order' ? 'active' : '' }}" 
                               data-bs-toggle="tab" 
                               href="#purchase-order" 
                               role="tab" 
                               aria-selected="{{ $activeTab === 'purchase-order' ? 'true' : 'false' }}">
                                <i class="ri-shopping-cart-line align-middle me-1"></i>
                                <span class="d-none d-sm-inline">Đơn Đặt Hàng</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <!-- Tab Nhặt Hàng -->
                        <div class="tab-pane {{ $activeTab === 'pick-order' ? 'active show' : '' }}" 
                             id="pick-order" 
                             role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="card-title mb-0">Danh Sách Đơn Cần Nhặt</h5>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                                                <i class="ri-upload-line align-middle me-1"></i>
                                                Tải Excel
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#uploadSaleworkModal">
                                                <i class="ri-database-2-line align-middle me-1"></i>
                                                Upload Salework
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Thống kê đầu bảng -->
                                    <div class="row mb-3">
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <div class="card border-primary">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-shopping-bag-line fs-1 text-primary"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 text-muted">Tổng SP cần nhặt</h6>
                                                            <h4 class="mb-0 fw-bold text-primary">{{ $pickOrders->count() }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <div class="card border-success">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-checkbox-circle-line fs-1 text-success"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 text-muted">Đã nhặt</h6>
                                                            <h4 class="mb-0 fw-bold text-success">
                                                                <span id="picked-count">{{ $pickOrders->where('status', 'picked')->sum('quantity_sold') }}</span>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <div class="card border-warning">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-add-circle-line fs-1 text-warning"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 text-muted">Đã thêm</h6>
                                                            <h4 class="mb-0 fw-bold text-warning">
                                                                <span id="added-count">{{ $pickOrders->sum(function($order) { $diff = ($order->quantity_sold ?? 0) - ($order->original_quantity ?? $order->quantity_sold ?? 0); return $diff > 0 ? $diff : 0; }) }}</span>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <div class="card border-danger">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-indeterminate-circle-line fs-1 text-danger"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 text-muted">Đã giảm</h6>
                                                            <h4 class="mb-0 fw-bold text-danger">
                                                                <span id="decreased-count">{{ abs($pickOrders->sum(function($order) { $diff = ($order->quantity_sold ?? 0) - ($order->original_quantity ?? $order->quantity_sold ?? 0); return $diff < 0 ? $diff : 0; })) }}</span>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bảng dữ liệu (Desktop) -->
                                    <div class="table-responsive d-none d-md-block mt-2">
                                        <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 80px;">Ảnh SP</th>
                                                    <th scope="col" style="width: 120px;">SKU</th>
                                                    <th scope="col">Tên sản phẩm</th>
                                                    <th scope="col" style="width: 100px;" class="text-center">Kệ</th>
                                                    <th scope="col" style="width: 100px;" class="text-center">Tồn kho</th>
                                                    <th scope="col" style="width: 120px;" class="text-center">Danh mục</th>
                                                    <th scope="col" style="width: 100px;" class="text-center">Số lượng</th>
                                                    <th scope="col" style="width: 150px;" class="text-center">Đã nhặt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pickOrders as $order)
                                                <tr class="{{ $order->status === 'picked' ? 'picked-row' : '' }} {{ ($order->status === 'picked' && ($order->quantity_sold ?? 0) == 0) ? 'zero-quantity' : '' }}">
                                                    <td>
                                                        <div class="avatar-sm">
                                                            @if($order->product_image)
                                                                <img src="{{ $order->product_image }}" 
                                                                     alt="Product Image" 
                                                                     class="img-thumbnail rounded"
                                                                     style="width: 60px; height: 60px; object-fit: cover;"
                                                                     title="Ảnh từ Salework API">
                                                            @else
                                                                <img src="{{ asset('assets/images/products/img-1.png') }}" 
                                                                     alt="Default Image" 
                                                                     class="img-thumbnail rounded"
                                                                     style="width: 60px; height: 60px; object-fit: cover; opacity: 0.5;"
                                                                     title="Ảnh mặc định - Không có từ API">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="fw-medium">{{ $order->sku ?? $order->product_code }}</span>
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1">{{ $order->product_name }}</h6>
                                                        <p class="text-muted mb-0 small">{{ $order->product_code }}</p>
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            // Phân tích tên sản phẩm để tìm ký hiệu kệ (ví dụ: A1_, A2_, B1_)
                                                            $productName = $order->product_name ?? '';
                                                            $shelfLabel = '';
                                                            $productCode = '';
                                                            $shelfColor = 'secondary';
                                                            
                                                            // Tìm pattern: một hoặc nhiều chữ cái, theo sau là một hoặc nhiều số, rồi dấu gạch dưới
                                                            if (preg_match('/^([A-Za-z]+)(\d+)_(.+)/', $productName, $matches)) {
                                                                $letter = strtoupper($matches[1]);
                                                                $number = $matches[2];
                                                                $remainingText = $matches[3];
                                                                $shelfLabel = "Kệ {$letter}{$number}";
                                                                
                                                                // Tìm mã sản phẩm trong phần còn lại (ví dụ: CR448_DEN, SET650)
                                                                // Pattern: tìm chuỗi có chữ hoa, số, dấu gạch dưới
                                                                if (preg_match('/([A-Z0-9_]+)/', $remainingText, $codeMatches)) {
                                                                    $matchedCode = $codeMatches[1];
                                                                    // Chỉ hiển thị nếu mã có từ 5 ký tự trở lên
                                                                    if (strlen($matchedCode) >= 5) {
                                                                        $productCode = $matchedCode;
                                                                    }
                                                                }
                                                                
                                                                // Mapping màu theo chữ cái kệ (không trùng lặp)
                                                                $colorMap = [
                                                                    'A' => 'primary', 'B' => 'success', 'C' => 'info', 'D' => 'warning',
                                                                    'E' => 'danger', 'F' => 'dark', 'G' => 'secondary', 'H' => 'primary',
                                                                    'I' => 'success', 'J' => 'info', 'K' => 'warning', 'L' => 'danger',
                                                                    'M' => 'dark', 'N' => 'secondary', 'O' => 'primary', 'P' => 'success',
                                                                    'Q' => 'info', 'R' => 'warning', 'S' => 'danger', 'T' => 'dark',
                                                                    'U' => 'secondary', 'V' => 'primary', 'W' => 'success', 'X' => 'info',
                                                                    'Y' => 'warning', 'Z' => 'danger'
                                                                ];
                                                                
                                                                // Tính toán màu độc nhất dựa trên ASCII
                                                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'dark', 'secondary'];
                                                                $colorIndex = (ord($letter) - ord('A')) % count($colors);
                                                                $shelfColor = $colors[$colorIndex];
                                                            }
                                                        @endphp
                                                        @if($shelfLabel)
                                                            <div>
                                                                <span class="badge bg-{{ $shelfColor }} fs-6 d-block mb-1">{{ $shelfLabel }}</span>
                                                                @if($productCode)
                                                                    <small class="text-muted d-block">{{ $productCode }}</small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info fs-6">{{ $order->stock ?? 0 }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $category = $order->category ?? 'N/A';
                                                            // Loại bỏ các từ loại sản phẩm
                                                            $keywordsToRemove = ['CROPTOP', 'SƠ MI', 'SET BỘ', 'VÁY ĐẦM', 'CHÂN VÁY'];
                                                            foreach ($keywordsToRemove as $keyword) {
                                                                $category = str_ireplace($keyword, '', $category);
                                                            }
                                                            // Loại bỏ khoảng trắng thừa và ký tự đặc biệt
                                                            $category = trim($category);
                                                            $category = preg_replace('/\s+/', ' ', $category); // Nhiều khoảng trắng thành 1
                                                            $category = trim($category, '|, '); // Loại bỏ dấu | và dấu phẩy ở đầu/cuối
                                                            if (empty($category)) {
                                                                $category = 'N/A';
                                                            }
                                                        @endphp
                                                        <span class="badge bg-secondary fs-6">{{ $category }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-danger decrease-quantity-btn" 
                                                                        data-order-id="{{ $order->id }}"
                                                                        style="min-width: 30px;">
                                                                    <i class="ri-subtract-line"></i>
                                                                </button>
                                                                <span class="badge bg-primary fs-6 quantity-display" 
                                                                      data-order-id="{{ $order->id }}"
                                                                      data-original-quantity="{{ $order->original_quantity ?? $order->quantity_sold ?? 0 }}"
                                                                      style="min-width: 50px;">
                                                                    {{ $order->quantity_sold ?? 0 }}
                                                                </span>
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-success increase-quantity-btn" 
                                                                        data-order-id="{{ $order->id }}"
                                                                        style="min-width: 30px;">
                                                                    <i class="ri-add-line"></i>
                                                                </button>
                                                            </div>
                                                            <small class="quantity-label text-muted" 
                                                                   data-order-id="{{ $order->id }}"
                                                                   style="font-size: 0.7rem; line-height: 1;">
                                                                @php
                                                                    $originalQty = $order->original_quantity ?? $order->quantity_sold ?? 0;
                                                                    $currentQty = $order->quantity_sold ?? 0;
                                                                    $difference = $currentQty - $originalQty;
                                                                @endphp
                                                                @if($difference > 0)
                                                                    <span class="text-success">+(+{{ $difference }})</span>
                                                                @elseif($difference < 0)
                                                                    <span class="text-danger">({{ $difference }})</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" 
                                                               class="form-check-input pick-order-checkbox" 
                                                               data-order-id="{{ $order->id }}"
                                                               {{ $order->status === 'picked' ? 'checked' : '' }}
                                                               style="width: 20px; height: 20px; cursor: pointer;">
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="ri-inbox-line fs-1 d-block mb-3"></i>
                                                            <h5 class="mb-0">Chưa có sản phẩm nào cần nhặt</h5>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Card View (Mobile) -->
                                    <div class="d-md-none mobile-card-container mt-2">
                                        @forelse($pickOrders as $order)
                                        @php
                                            // Phân tích tên sản phẩm để tìm ký hiệu kệ
                                            $productName = $order->product_name ?? '';
                                            $shelfLabel = '';
                                            $productCode = '';
                                            $shelfColor = 'secondary';
                                            
                                            if (preg_match('/^([A-Za-z]+)(\d+)_(.+)/', $productName, $matches)) {
                                                $letter = strtoupper($matches[1]);
                                                $number = $matches[2];
                                                $remainingText = $matches[3];
                                                $shelfLabel = "Kệ {$letter}{$number}";
                                                
                                                if (preg_match('/([A-Z0-9_]+)/', $remainingText, $codeMatches)) {
                                                    $matchedCode = $codeMatches[1];
                                                    if (strlen($matchedCode) >= 5) {
                                                        $productCode = $matchedCode;
                                                    }
                                                }
                                                
                                                // Tính toán màu độc nhất dựa trên ASCII
                                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'dark', 'secondary'];
                                                $colorIndex = (ord($letter) - ord('A')) % count($colors);
                                                $shelfColor = $colors[$colorIndex];
                                            }
                                        @endphp
                                        <div class="card mb-3 {{ $order->status === 'picked' ? 'picked-row' : '' }} {{ ($order->status === 'picked' && ($order->quantity_sold ?? 0) == 0) ? 'zero-quantity' : '' }}">
                                            <div class="card-body">
                                                <div class="d-flex gap-3">
                                                    <!-- Ảnh sản phẩm -->
                                                    <div class="flex-shrink-0">
                                                        @if($order->product_image)
                                                            <img src="{{ $order->product_image }}" 
                                                                 alt="Product Image" 
                                                                 class="img-thumbnail rounded"
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('assets/images/products/img-1.png') }}" 
                                                                 alt="Default Image" 
                                                                 class="img-thumbnail rounded"
                                                                 style="width: 80px; height: 80px; object-fit: cover; opacity: 0.5;">
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Thông tin sản phẩm -->
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $order->product_name }}</h6>
                                                        <p class="text-muted mb-1 small">{{ $order->sku ?? $order->product_code }}</p>
                                                        
                                                        <!-- Badges -->
                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                            @if($shelfLabel)
                                                                <span class="badge bg-{{ $shelfColor }}">{{ $shelfLabel }}</span>
                                                                @if($productCode)
                                                                    <small class="text-muted align-self-center">{{ $productCode }}</small>
                                                                @endif
                                                            @endif
                                                            <span class="badge bg-info">Tồn: {{ $order->stock ?? 0 }}</span>
                                                            @php
                                                                $category = $order->category ?? 'N/A';
                                                                // Loại bỏ các từ loại sản phẩm
                                                                $keywordsToRemove = ['CROPTOP', 'SƠ MI', 'SET BỘ', 'VÁY ĐẦM', 'CHÂN VÁY'];
                                                                foreach ($keywordsToRemove as $keyword) {
                                                                    $category = str_ireplace($keyword, '', $category);
                                                                }
                                                                // Loại bỏ khoảng trắng thừa và ký tự đặc biệt
                                                                $category = trim($category);
                                                                $category = preg_replace('/\s+/', ' ', $category); // Nhiều khoảng trắng thành 1
                                                                $category = trim($category, '|, '); // Loại bỏ dấu | và dấu phẩy ở đầu/cuối
                                                                if (empty($category)) {
                                                                    $category = 'N/A';
                                                                }
                                                            @endphp
                                                            <span class="badge bg-secondary">{{ $category }}</span>
                                                        </div>
                                                        
                                                        <!-- Số lượng và checkbox -->
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex flex-column align-items-center gap-1">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger decrease-quantity-btn" 
                                                                            data-order-id="{{ $order->id }}">
                                                                        <i class="ri-subtract-line"></i>
                                                                    </button>
                                                                    <span class="badge bg-primary quantity-display" 
                                                                          data-order-id="{{ $order->id }}"
                                                                          data-original-quantity="{{ $order->original_quantity ?? $order->quantity_sold ?? 0 }}">
                                                                        {{ $order->quantity_sold ?? 0 }}
                                                                    </span>
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-success increase-quantity-btn" 
                                                                            data-order-id="{{ $order->id }}">
                                                                        <i class="ri-add-line"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="quantity-label text-muted" 
                                                                       data-order-id="{{ $order->id }}"
                                                                       style="font-size: 0.65rem; line-height: 1;">
                                                                    @php
                                                                        $originalQty = $order->original_quantity ?? $order->quantity_sold ?? 0;
                                                                        $currentQty = $order->quantity_sold ?? 0;
                                                                        $difference = $currentQty - $originalQty;
                                                                    @endphp
                                                                    @if($difference > 0)
                                                                        <span class="text-success">+(+{{ $difference }})</span>
                                                                    @elseif($difference < 0)
                                                                        <span class="text-danger">({{ $difference }})</span>
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            <input type="checkbox" 
                                                                   class="form-check-input pick-order-checkbox" 
                                                                   data-order-id="{{ $order->id }}"
                                                                   {{ $order->status === 'picked' ? 'checked' : '' }}
                                                                   style="width: 24px; height: 24px; cursor: pointer;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri-inbox-line fs-1 d-block mb-3"></i>
                                                <h5 class="mb-0">Chưa có sản phẩm nào cần nhặt</h5>
                                            </div>
                                        </div>
                                        @endforelse
                                    </div>

                                    <!-- Thống kê -->
                                    @if($pickOrders->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="text-muted">
                                            Hiển thị {{ $pickOrders->count() }} sản phẩm cần nhặt
                                        </div>
                                        <div class="text-muted">
                                            <i class="ri-archive-line align-middle me-1"></i>
                                            Tổng: {{ $pickOrders->sum('quantity') }} sản phẩm
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tab Đơn Đặt Hàng -->
                        <div class="tab-pane {{ $activeTab === 'purchase-order' ? 'active show' : '' }}" 
                             id="purchase-order" 
                             role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="card-title mb-0">Danh Sách Đơn Đặt Hàng</h5>
                                        <button type="button" class="btn btn-success btn-sm">
                                            <i class="ri-add-line align-middle me-1"></i>
                                            Tạo Đơn Đặt Hàng
                                        </button>
                                    </div>

                                    <!-- Bộ lọc -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-auto">
                                            <input type="text" class="form-control" placeholder="Tìm kiếm mã đơn...">
                                        </div>
                                        <div class="col-sm-auto">
                                            <select class="form-select">
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="pending">Chờ xử lý</option>
                                                <option value="ordered">Đã đặt</option>
                                                <option value="received">Đã nhận</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-auto">
                                            <button type="button" class="btn btn-soft-secondary">
                                                <i class="ri-filter-3-line align-middle me-1"></i> Lọc
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bảng dữ liệu -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 50px;">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checkAllPurchase">
                                                        </div>
                                                    </th>
                                                    <th scope="col">Mã Đơn</th>
                                                    <th scope="col">Nhà Cung Cấp</th>
                                                    <th scope="col">Sản Phẩm</th>
                                                    <th scope="col">Số Lượng</th>
                                                    <th scope="col">Giá Trị</th>
                                                    <th scope="col">Trạng Thái</th>
                                                    <th scope="col">Ngày Đặt</th>
                                                    <th scope="col">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($purchaseOrders as $order)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="{{ $order->id }}">
                                                        </div>
                                                    </td>
                                                    <td><strong>#PO-{{ $order->id }}</strong></td>
                                                    <td>{{ $order->supplier_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->product_name ?? 'N/A' }}</td>
                                                    <td>{{ $order->quantity ?? 0 }}</td>
                                                    <td>{{ number_format($order->total_amount ?? 0) }} VNĐ</td>
                                                    <td>
                                                        <span class="badge bg-success">Đã đặt</span>
                                                    </td>
                                                    <td>{{ $order->created_at->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="ri-more-fill"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>Xem</a></li>
                                                                <li><a class="dropdown-item" href="#"><i class="ri-check-line me-2"></i>Xác nhận nhận hàng</a></li>
                                                                <li><a class="dropdown-item" href="#"><i class="ri-edit-line me-2"></i>Sửa</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="ri-shopping-cart-line fs-1 d-block mb-2"></i>
                                                            <p class="mb-0">Chưa có đơn đặt hàng nào</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Thống kê -->
                                    @if($purchaseOrders->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="text-muted">
                                            Hiển thị {{ $purchaseOrders->count() }} đơn đặt hàng
                                        </div>
                                        <div class="text-muted">
                                            <i class="ri-shopping-cart-line align-middle me-1"></i>
                                            Tổng giá trị: {{ number_format($purchaseOrders->sum('total_amount')) }} VNĐ
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Excel -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">
                    <i class="ri-upload-line align-middle me-2"></i>
                    Tải File Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadExcelForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Chọn file Excel</label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Định dạng file: Excel (.xlsx, .xls) hoặc CSV<br>
                            Cấu trúc: Cột 1 (SKU), Cột 2 (Tên sản phẩm), Cột 3 (Số lượng bán)
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="ri-alert-line me-1"></i> Lưu ý:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Dữ liệu cũ sẽ bị thay thế hoàn toàn</strong> khi upload file mới</li>
                            <li>File Excel phải có 3 cột: SKU, Tên sản phẩm, Số lượng bán</li>
                            <li>Dòng đầu tiên là tiêu đề (sẽ được bỏ qua)</li>
                            <li>Hệ thống sẽ tự động lấy ảnh, tồn kho và danh mục từ API Salework dựa trên SKU</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" id="uploadExcelBtn">
                    <i class="ri-upload-line align-middle me-1"></i>
                    Tải Lên
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Salework -->
<div class="modal fade" id="uploadSaleworkModal" tabindex="-1" aria-labelledby="uploadSaleworkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSaleworkModalLabel">
                    <i class="ri-database-2-line align-middle me-2"></i>
                    Upload Dữ Liệu Salework
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Hướng dẫn:</strong> File Excel phải có các cột theo thứ tự:
                    <br>MÃ SẢN PHẨM | TÊN SẢN PHẨM | ĐƠN VỊ TÍNH | THUẾ GTGT | GIÁ NHẬP | GIÁ BÁN BUÔN | GIÁ BÁN LẺ | BARCODE | TỒN KHO | GIỮ HÀNG | LINK ẢNH | DANH MỤC
                </div>
                
                <form id="uploadSaleworkForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="saleworkFile" class="form-label">
                            <i class="ri-file-excel-line me-1"></i>
                            Chọn File Excel Salework
                        </label>
                        <input type="file" class="form-control" id="saleworkFile" name="salework_file" accept=".xlsx,.xls" required>
                        <div class="form-text">Chỉ chấp nhận file Excel (.xlsx, .xls) - Tối đa 4MB</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="clearExisting" name="clear_existing">
                            <label class="form-check-label" for="clearExisting">
                                Xóa dữ liệu cũ trước khi import (Cảnh báo: Sẽ xóa tất cả dữ liệu Salework hiện tại)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-info" id="uploadSaleworkBtn">
                    <i class="ri-database-2-line align-middle me-1"></i>
                    Upload Salework
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs-custom .nav-link {
        border: 1px solid transparent;
        border-radius: 0.25rem 0.25rem 0 0;
        font-weight: 500;
    }
    
    .nav-tabs-custom .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .picked-row {
        background-color: #d4edda !important;
    }

    .picked-row td {
        font-weight: 500;
    }

    /* Mobile card picked */
    .card.picked-row {
        background-color: #d4edda !important;
        border-color: #c3e6cb !important;
    }

    /* Picked row với số lượng = 0 thì màu đỏ */
    .picked-row.zero-quantity {
        background-color: #f8d7da !important;
    }

    .picked-row.zero-quantity td {
        font-weight: 500;
    }

    /* Mobile card picked với số lượng = 0 */
    .card.picked-row.zero-quantity {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }

    /* Fixed header khi cuộn */
    .table-responsive {
        height: calc(100vh - 400px);
        overflow-y: auto;
    }

    .table-responsive thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
        border-top: 4px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 0;
        padding-top: 0;
    }

    .table-responsive thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        border-top: 4px solid #dee2e6;
        padding-top: 0.5rem;
    }

    /* Mobile cards - cuộn toàn bộ trang thay vì chỉ container */
    @media (max-width: 767.98px) {
        .table-responsive thead {
            position: relative;
            top: auto;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Xử lý upload Excel
    $('#uploadExcelBtn').click(function() {
        const form = $('#uploadExcelForm')[0];
        const formData = new FormData(form);
        
        if (!formData.get('excel_file')) {
            showToast('Vui lòng chọn file Excel', 'danger');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="ri-loader-4-line align-middle me-1"></i> Đang tải...');
        
        $.ajax({
            url: '{{ route("admin.pick_order.upload_excel") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#uploadExcelModal').modal('hide');
                    location.reload(); // Reload trang để hiển thị dữ liệu mới
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Lỗi khi tải file', 'danger');
            },
            complete: function() {
                $('#uploadExcelBtn').prop('disabled', false).html('<i class="ri-upload-line align-middle me-1"></i> Tải Lên');
            }
        });
    });

    // Xử lý upload Salework
    $('#uploadSaleworkBtn').click(function() {
        const form = $('#uploadSaleworkForm')[0];
        const formData = new FormData(form);
        
        if (!formData.get('salework_file')) {
            showToast('Vui lòng chọn file Excel Salework', 'danger');
            return;
        }
        
        // Kiểm tra kích thước file (4MB limit)
        const fileInput = $('#saleworkFile')[0];
        if (fileInput.files && fileInput.files[0]) {
            const fileSize = fileInput.files[0].size;
            if (fileSize > 4 * 1024 * 1024) { // 4MB
                showToast('Kích thước file không được vượt quá 4MB (Giới hạn PHP)', 'danger');
                return;
            }
        }
        
        // Debug: Log form data
        console.log('Form data check:', {
            hasFile: formData.has('salework_file'),
            hasToken: formData.has('_token'),
            fileSize: fileInput.files[0]?.size,
            fileName: fileInput.files[0]?.name
        });
        
        $(this).prop('disabled', true).html('<i class="ri-loader-4-line align-middle me-1"></i> Đang tải...');
        
        $.ajax({
            url: '{{ route("admin.pick_order.upload_salework") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    let message = response.message;
                    if (response.imported) {
                        message += ` (Đã import ${response.imported} sản phẩm)`;
                    }
                    showToast(message, 'success');
                    $('#uploadSaleworkModal').modal('hide');
                    location.reload(); // Reload trang để hiển thị dữ liệu mới
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Lỗi khi tải file Salework', 'danger');
            },
            complete: function() {
                $('#uploadSaleworkBtn').prop('disabled', false).html('<i class="ri-database-2-line align-middle me-1"></i> Upload Salework');
            }
        });
    });


    // Xử lý tăng số lượng
    $(document).on('click', '.increase-quantity-btn', function() {
        const btn = $(this);
        const orderId = btn.data('order-id');
        const row = btn.closest('tr, .card');
        const quantityDisplay = row.find('.quantity-display[data-order-id="' + orderId + '"]');
        const currentQuantity = parseInt(quantityDisplay.text()) || 0;
        const newQuantity = currentQuantity + 1;
        
        updateQuantity(orderId, newQuantity, quantityDisplay);
    });

    // Xử lý giảm số lượng
    $(document).on('click', '.decrease-quantity-btn', function() {
        const btn = $(this);
        const orderId = btn.data('order-id');
        const row = btn.closest('tr, .card');
        const quantityDisplay = row.find('.quantity-display[data-order-id="' + orderId + '"]');
        const currentQuantity = parseInt(quantityDisplay.text()) || 0;
        
        if (currentQuantity > 0) {
            const newQuantity = currentQuantity - 1;
            updateQuantity(orderId, newQuantity, quantityDisplay);
        } else {
            showToast('Số lượng không thể nhỏ hơn 0', 'warning');
        }
    });

    // Hàm cập nhật số lượng
    function updateQuantity(orderId, newQuantity, quantityDisplay) {
        $.ajax({
            url: '/nhat-hang/update-quantity/' + orderId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                quantity: newQuantity
            },
            success: function(response) {
                if (response.success) {
                    console.log('Before update:', $('.quantity-display[data-order-id="' + orderId + '"]').first().text());
                    
                    // Cập nhật tất cả quantity-display có cùng orderId (desktop + mobile)
                    $('.quantity-display[data-order-id="' + orderId + '"]').text(newQuantity);
                    
                    console.log('After update:', $('.quantity-display[data-order-id="' + orderId + '"]').first().text());
                    
                    // Cập nhật class zero-quantity cho tất cả rows/cards có cùng orderId
                    const allRows = $('tr, .card').filter(function() {
                        return $(this).find('.quantity-display[data-order-id="' + orderId + '"]').length > 0;
                    });
                    
                    // Kiểm tra checkbox có được tick không
                    const checkbox = $('.pick-order-checkbox[data-order-id="' + orderId + '"]').first();
                    const isPicked = checkbox.is(':checked');
                    
                    // Cập nhật màu dựa trên số lượng và trạng thái picked
                    if (isPicked) {
                        if (newQuantity === 0) {
                            allRows.addClass('zero-quantity');
                        } else {
                            allRows.removeClass('zero-quantity');
                        }
                    }
                    
                    // Hiển thị label thay đổi
                    const originalQuantity = parseInt(quantityDisplay.data('original-quantity')) || 0;
                    const difference = newQuantity - originalQuantity;
                    
                    // Cập nhật tất cả labels có cùng orderId
                    const labelElements = $('.quantity-label[data-order-id="' + orderId + '"]');
                    
                    if (difference > 0) {
                        labelElements.text('+(' + difference + ')').removeClass('text-danger').addClass('text-success');
                    } else if (difference < 0) {
                        labelElements.text('(' + difference + ')').removeClass('text-success').addClass('text-danger');
                    } else {
                        labelElements.text('');
                    }
                    
                    // Tính toán lại thống kê dựa trên dữ liệu thật (delay để đảm bảo DOM đã update)
                    setTimeout(function() {
                        recalculateStatistics();
                    }, 50);
                    
                    showToast('Đã cập nhật số lượng thành ' + newQuantity, 'success');
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Lỗi khi cập nhật số lượng', 'danger');
            }
        });
    }

    // Hàm tính toán lại thống kê từ dữ liệu thật
    function recalculateStatistics() {
        let totalAdded = 0;
        let totalDecreased = 0;
        const processedOrders = new Set(); // Để tránh đếm trùng desktop và mobile
        
        console.log('=== Recalculating Statistics ===');
        
        // Duyệt qua tất cả các quantity-display
        $('.quantity-display').each(function() {
            const quantityDisplay = $(this);
            const orderId = quantityDisplay.data('order-id');
            
            // Nếu đã xử lý order này rồi thì bỏ qua
            if (processedOrders.has(orderId)) {
                console.log('Skipping duplicate Order ID:', orderId);
                return;
            }
            
            processedOrders.add(orderId);
            
            const currentQuantity = parseInt(quantityDisplay.text()) || 0;
            const originalQuantity = parseInt(quantityDisplay.data('original-quantity')) || 0;
            const difference = currentQuantity - originalQuantity;
            
            console.log('Order ID:', orderId, '| Text:', quantityDisplay.text(), '| Current:', currentQuantity, '| Original:', originalQuantity, '| Diff:', difference);
            
            if (difference > 0) {
                totalAdded += difference;
                console.log('  -> Adding to totalAdded:', difference, 'New total:', totalAdded);
            } else if (difference < 0) {
                totalDecreased += Math.abs(difference);
                console.log('  -> Adding to totalDecreased:', Math.abs(difference), 'New total:', totalDecreased);
            }
        });
        
        console.log('=== Final Results ===');
        console.log('Total Added:', totalAdded);
        console.log('Total Decreased:', totalDecreased);
        
        // Cập nhật UI
        $('#added-count').text(totalAdded);
        $('#decreased-count').text(totalDecreased);
    }


    // Xử lý khi tick checkbox "Đã nhặt"
    $(document).on('change', '.pick-order-checkbox', function() {
        const checkbox = $(this);
        const orderId = checkbox.data('order-id');
        const row = checkbox.closest('tr, .card');
        const isChecked = checkbox.is(':checked');
        
        // Gọi API để cập nhật trạng thái
        $.ajax({
            url: '/nhat-hang/mark-picked/' + orderId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: isChecked ? 'picked' : 'pending'
            },
            success: function(response) {
                if (response.success) {
                    // Lấy số lượng sản phẩm từ row
                    const quantityElement = row.find('.quantity-display[data-order-id="' + orderId + '"]');
                    const quantity = parseInt(quantityElement.text()) || 0;
                    
                    // Cập nhật giao diện
                    if (isChecked) {
                        row.addClass('picked-row');
                        // Nếu số lượng = 0 thì thêm class zero-quantity (màu đỏ)
                        if (quantity === 0) {
                            row.addClass('zero-quantity');
                        } else {
                            row.removeClass('zero-quantity');
                        }
                        showToast('Đã đánh dấu sản phẩm đã nhặt', 'success');
                        // Cập nhật số lượng đã nhặt (theo số lượng sản phẩm)
                        updatePickedCount(quantity);
                    } else {
                        row.removeClass('picked-row zero-quantity');
                        showToast('Đã bỏ đánh dấu sản phẩm', 'info');
                        // Cập nhật số lượng đã nhặt (trừ đi số lượng sản phẩm)
                        updatePickedCount(-quantity);
                    }
                } else {
                    // Revert checkbox nếu lỗi
                    checkbox.prop('checked', !isChecked);
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                // Revert checkbox nếu lỗi
                checkbox.prop('checked', !isChecked);
                const response = xhr.responseJSON;
                showToast(response?.message || 'Lỗi khi cập nhật', 'danger');
            }
        });
    });


    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 show" 
                 role="alert" 
                 aria-live="assertive" 
                 aria-atomic="true"
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ri-check-line align-middle me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        
        setTimeout(function() {
            $('.toast').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Cập nhật số lượng đã nhặt
    function updatePickedCount(delta) {
        const countElement = $('#picked-count');
        const currentCount = parseInt(countElement.text()) || 0;
        const newCount = Math.max(0, currentCount + delta);
        countElement.text(newCount);
    }

});
</script>

@endsection

