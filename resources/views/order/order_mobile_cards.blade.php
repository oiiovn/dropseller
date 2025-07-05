{{-- Component: Mobile Order Cards + Filter + Pagination --}}
<div class="mobile-card-container">
    <!-- Mobile Order Cards UI giống hình user gửi, cập nhật search bar động -->
    <div id="mobile-filter-bar-wrap ">
        <div id="mobile-filter-bar" class=" mb-1 mobile-order-filter-bar d-flex align-items-center gap-2 p-2" style="background:#fff;  position:sticky;top:0;z-index:10; transition:all .3s;">
            <select class="form-select form-select-sm flex-grow-1" id="mobileShopFilter" style="max-width:110px; min-width:80px;">
                <option value="">All shops</option>
                @foreach($shops ?? [] as $shop)
                    <option value="{{$shop->shop_name}}">{{$shop->shop_name}}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" id="mobilePaymentFilter" style=" color:#666;" >
                <option value="">Thanh toán</option>
                <option value="Đã thanh toán">Đã thanh toán</option>
                <option value="Chưa thanh toán">Chưa thanh toán</option>
            </select>
            <select class="form-select form-select-sm" id="mobileReconciledFilter" style="color:#666;">
                <option value="">Đối soát</option>
                <option value="Đã đối soát">Đã đối soát</option>
                <option value="Chưa đối soát">Chưa đối soát</option>
            </select>
            
            <button class="btn btn-link p-0" id="mobileShowDatePicker" style="font-size:22px; color:#555; position:relative;">
                <i class="ri-calendar-line"></i>
                <input type="date" id="mobileDateInput" style="position:absolute; left:0; top:0; width:100%; height:100%; opacity:0; cursor:pointer;">
            </button>
            <button class="btn btn-link p-0" id="mobileShowSearchBar" style="font-size:22px; color:#555;">
                <i class="ri-search-line"></i>
            </button>
        </div>
        <div id="mobile-search-bar" class="d-none" style=" border-radius:10px; margin:8px; display:flex; align-items:center;  position:relative; transition:all .3s;">
           <div style="display:flex; align-items:center; justify-content:start; width:90%; background:#F5F5F5; border-radius:10px;">
                <input type="text" class="form-control border-0" id="mobileSearchInput" placeholder="Tìm kiếm đơn hàng, shop, mã thanh toán ..." style="box-shadow:none;  background:#F5F5F5; border-radius:10px; width:100%; align-items:start; justify-content:start;">
                <button class="btn btn-link p-0" style="font-size:20px; color:#555;margin-right:10px;">
                    <i class="ri-search-line"></i>
                </button>
            </div>
            <button class="btn btn-danger btn-sm ms-2" id="mobileCloseSearchBar" style="border-radius:50%; width:28px; height:28px; display:flex; align-items:center; justify-content:center; position:absolute; right:4px; opacity:0.8; ">
                <i class="ri-close-line" style="font-size:18px;"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile Cards Container with Pagination -->
    <div class="mobile-card-list pb-2 " id="mobileCardList">
        @foreach($orders as $item)
        @php
            // Format filter_date to show only the first date
            $displayDate = $item->filter_date;
            if (strpos($item->filter_date, ' - ') !== false) {
                $dateParts = explode(' - ', $item->filter_date);
                $displayDate = $dateParts[0];
            }
        @endphp
        <div class="mobile-order-card mb-2 py-2 px-3" 
             data-filter-date="{{$item->filter_date}}"
             data-payment-status="{{$item->payment_status}}"
             data-reconciled="{{$item->reconciled == 1 ? 'Chưa đối soát' : 'Đã đối soát'}}"
             data-order-code="{{$item->order_code}}"
             data-shop-name="{{ $item->shop->shop_name ?? 'N/A' }}"
             data-created-at="{{$item->created_at->format('d/m/Y H:i')}}"
             style="background:#fff; border:1px solid #e0e0e0; border-radius:12px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
        <div class="d-flex align-items-center">
        <span class="fs-12 fw-bold" style="color: #4EBBD3;">{{$item->order_code}}</span>    
        <span class="ms-auto" style="font-size:13px;color:#666;">Số lượng: <span class="">{{$item->total_products}}</span></span>
        </div>   
        <div class="d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center">
                @if($item->shop->platform == 'Tiktok')
                    <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="TikTok" style="width:25px;height:25px;" class="me-2">
                @elseif($item->shop->platform == 'Shoppe')
                    <img src="https://img.icons8.com/fluency/240/shopee.png" alt="Shopee" style="width:25px;height:25px;" class="me-2">
                @else
                    <div style="width:25px;height:25px;background:#ccc;border-radius:4px;" class="me-2"></div>
                @endif

                <span class="fw-bold" style="font-size:12px; line-height: 22px;">{{ $item->shop->shop_name ?? 'N/A' }}</span>
            </div> 
            <span class="text-end" style="font-size:12px;color:#5D5D5D;line-height: 22px; color: #666;">Phí drop : <span class="">{{ number_format($item->total_dropship, 0, ',', '.') }} VNĐ</span></span>              
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="" style="font-size:12px;color:#888;line-height: 22px;">Ngày tạo: {{$displayDate}}</span>
                <span class="fw-bold" style="font-size:15px;color: #4EBBD3;">{{ number_format($item->total_bill, 0, ',', '.') }}<span class="fs-15" style="color: #888;"> VNĐ</span></span>
            </div>
            
           
            <div class="d-flex align-items-center justify-content-between pt-2" style="font-size:13px; border-top:1px solid #eee;">
                <div class="text-muted">{{$item->transaction_id}}</div>
               <div class="d-flex align-items-center justify-content-between gap-2">
               <div class="@if($item->payment_status=='Đã thanh toán') text-success @else text-danger @endif">{{$item->payment_status}}</div>
                <div class=" @if($item->reconciled==0) text-success @else text-warning @endif">
                    @if($item->reconciled==0) Đã đối soát @else Chưa đối soát @endif
                </div>
               </div>
                
            </div>
            <!-- Nút ẩn để JS trigger modal chi tiết -->
            <button 
                class="view-order-btn d-none"
                data-order-id="{{ $item->id }}"
                data-order-code="{{ $item->order_code }}"
                data-shop-name="{{ $item->shop->shop_name ?? 'N/A' }}"
                data-filter-date="{{ $item->filter_date }}"
                data-total-products="{{ $item->total_products }}"
                data-total-dropship="{{ $item->total_dropship }}"
                data-total-bill="{{ $item->total_bill }}"
                data-order-details='@json($item->orderDetails->toArray())'>
            </button>
        </div>
        @endforeach
    </div>
    
    <!-- Mobile Pagination -->
    <div class="mobile-pagination-container d-flex justify-content-between align-items-center p-3" style="background:#f8f9fa; border-top:1px solid #eee;">
        
        <div class="mobile-pagination-controls d-flex gap-2 m-auto">
            <button class="btn btn-sm btn-outline-primary" id="mobilePrevPage" disabled>
                <i class="ri-arrow-left-s-line"></i> Trước
            </button>
            <div class="d-flex align-items-center gap-1">
                <span class="text-muted">Trang</span>
                <span class="fw-bold" id="mobileCurrentPage">1</span>
                <span class="text-muted">/</span>
                <span class="fw-bold" id="mobileTotalPages">1</span>
            </div>
            <button class="btn btn-sm btn-outline-primary" id="mobileNextPage">
                Tiếp <i class="ri-arrow-right-s-line"></i>
            </button>
        </div>
    </div>
    
    <!-- No Data Message for Mobile -->
    <div class="no-data-message text-center py-5" id="mobileNoDataMessage" style="display: none;">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
            <h5 class="text-muted mb-2">Không tìm thấy dữ liệu</h5>
            <p class="text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc hiện tại</p>
            <button class="btn btn-outline-primary mt-3" onclick="clearMobileFilters()">
                <i class="ri-refresh-line me-1"></i>Xóa bộ lọc
            </button>
        </div>
    </div>
    
    <style>
    .mobile-card-list .mobile-order-card {transition:box-shadow .2s;}
    .mobile-card-list .mobile-order-card:hover {box-shadow:0 2px 8px rgba(63,135,177,0.08);}
    #mobile-search-bar input:focus {outline:none; box-shadow:none;}
    
    /* Mobile Pagination Styles */
    .mobile-pagination-container {
        position: sticky;
        bottom: 0;
        z-index: 5;
        background: rgba(248, 249, 250, 0.95);
        backdrop-filter: blur(10px);
    }
    
    .mobile-pagination-controls .btn {
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 6px;
    }
    
    .mobile-pagination-controls .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .mobile-pagination-info small {
        font-size: 11px;
    }
    </style>
</div>
<script src="{{ asset('assets/js/order-mobile.js') }}"></script>