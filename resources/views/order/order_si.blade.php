@extends('layout')
@section('title', 'main')

@section('main')

<!-- Include CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/order-page.css') }}">

<!-- Include JavaScript -->
<script src="{{ asset('assets/js/order-page.js') }}"></script>

<div class="" style=" width: 100%; background: white; overflow: hidden; ">
    <div class="row " style="overflow: hidden;">
        <div class="col-lg-12 h-lg-[calc(100vh-80px)] h-full">
            <div class="" id="orderList">
                <div class="pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success justify-content-start justify-content-md-start d-none d-md-flex" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> <span class="d-none d-md-inline">Tất cả đơn hàng</span><span class="d-inline d-md-none">All</span>
                                </a>
                            </li>
                            @foreach($shops as $shop)
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered" data-bs-toggle="tab" id="shop-{{$shop->shop_id}}" href="#shop-{{$shop->shop_id}}-content" role="tab" aria-selected="false">
                                    @if($shop->platform == 'Tiktok')
                                    <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                    @elseif($shop->platform == 'Shoppe')
                                    <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                    @else
                                    <i class="fas fa-store me-1"></i>
                                    @endif
                                    {{$shop->shop_name}}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <!-- Tất cả đơn hàng -->
                            <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                @if($orders->count() == 0)
                                    <div class="text-center py-5">
                                        <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
                                        <h5 class="text-muted mb-2">Không có đơn hàng nào</h5>
                                        <p class="text-muted mb-0">Tài khoản này hiện chưa có đơn hàng nào.</p>
                                    </div>
                                @else
                                    @include('order.components.order-filters')
                                    @include('order.components.order-table')
                                    @include('order.order_mobile_cards')
                                @endif
                                <script>
                                    $(document).ready(function() {
                                // All functionality is now handled by OrderPage class in order-page.js
                            });
                            </script>
                            </div>
                            @foreach($shops as $shop)
                            <div class="tab-pane fade" id="shop-{{$shop->shop_id}}-content" role="tabpanel">
                                @if($orders->where('shop_id', $shop->shop_id)->count() == 0)
                                    <div class="text-center py-5">
                                        <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
                                        <h5 class="text-muted mb-2">Không có đơn hàng cho shop này</h5>
                                    </div>
                                @else
                                    @include('order.components.shop-filters', ['shop' => $shop])
                                    @include('order.components.shop-table', ['shop' => $shop, 'orders' => $orders])
                                    <div class="mobile-card-container">
                                        @foreach($orders->where('shop_id', $shop->shop_id) as $order)
                                        @php
                                            // Format filter_date to show only the first date
                                            $displayDate = $order->filter_date;
                                            if (strpos($order->filter_date, ' - ') !== false) {
                                                $dateParts = explode(' - ', $order->filter_date);
                                                $displayDate = $dateParts[0];
                                            }
                                        @endphp
                                        <div class="mobile-order-card" 
                                             data-created-at="{{$order->created_at}}"
                                             data-filter-date="{{$order->filter_date}}"
                                             data-payment-status="{{$order->payment_status}}"
                                             data-reconciled="{{$order->reconciled == 1 ? 'Chưa đối soát' : 'Đã đối soát'}}"
                                             data-order-code="{{$order->order_code}}"
                                             data-shop-name="{{$shop->shop_name ?? 'N/A'}}">
                                            
                                            <!-- Header với platform icon và shop name -->
                                            <div class="mobile-card-header">
                                                @if($shop->platform == 'Tiktok')
                                                <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="TikTok" class="mobile-platform-icon">
                                                @elseif($shop->platform == 'Shoppe')
                                                <img src="https://img.icons8.com/fluency/240/shopee.png" alt="Shopee" class="mobile-platform-icon">
                                                @else
                                                <div class="mobile-platform-icon" style="background: #ccc; border-radius: 4px;"></div>
                                                @endif
                                                <span class="mobile-shop-name">{{ $shop->shop_name ?? 'N/A' }}</span>
                                                                        </div>

                                            <!-- Order Info -->
                                            <div class="mobile-order-info">
                                                <div class="mobile-order-code hienthicopy" data-order-code="{{$order->order_code}}">
                                                    {{$order->order_code}}
                                                                            </div>
                                                <div class="mobile-order-date">{{$displayDate}}</div>
                                                                            </div>

                                            <!-- Products count và amount -->
                                            <div class="mobile-order-details">
                                                <span class="mobile-products-count">Sản phẩm: {{$order->total_products}}</span>
                                                <span class="mobile-amount">{{ number_format($order->total_bill, 0, ',', '.') }} VND</span>
                                                                            </div>

                                            <!-- Status pills -->
                                            <div class="mobile-status-section">
                                                @if($order->payment_status == 'Chưa thanh toán')
                                                <span class="mobile-status-pill mobile-status-unpaid">
                                                    Chưa thanh toán
                                                </span>
                                                @else
                                                <span class="mobile-status-pill mobile-status-paid">
                                                    Đã thanh toán
                                                </span>
                                                @endif

                                                @if($order->reconciled == 1)
                                                <span class="mobile-status-pill mobile-status-not-reconciled">
                                                    Chưa đối soát
                                                </span>
                                                @else
                                                <span class="mobile-status-pill mobile-status-reconciled">
                                                    Đã đối soát
                                                </span>
                                                @endif
                                                                        </div>
                                            
                                            <!-- Detail button -->
                                            <button class="mobile-detail-btn view-order-btn" 
                                                 data-order-id="{{$order->id}}"
                                                 data-order-code="{{$order->order_code}}"
                                                 data-shop-name="{{ $order->shop->shop_name ?? 'N/A' }}"
                                                 data-filter-date="{{$order->filter_date}}"
                                                 data-total-products="{{$order->total_products}}"
                                                 data-total-dropship="{{$order->total_dropship}}"
                                                 data-total-bill="{{$order->total_bill}}"
                                                 data-order-details='@json($order->orderDetails->toArray())'>
                                                <i class="ri-eye-line"></i>Chi tiết
                                            </button>
                                                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <script>
                                    $(document).ready(function() {
                                // Shop DataTable initialization is now handled by ShopOrderPage class in order-page.js

                                    // All shop functionality is now handled by ShopOrderPage class in order-page.js
                                });
                                </script>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
    const shopId = td.dataset.shopId;
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });

// Debug function to test filtering
window.debugFiltering = function() {
    console.log('=== FILTER DEBUG ===');
    
    // Main table
    const mainDateFilter = document.getElementById('dateFilter');
    const mainPaymentFilter = document.getElementById('paymentFilter'); 
    const mainReconciledFilter = document.getElementById('reconciledFilter');
    
    console.log('Main filters:', {
        date: mainDateFilter?.value,
        payment: mainPaymentFilter?.value,
        reconciled: mainReconciledFilter?.value
    });
    
    // Check first few rows data
    const mainRows = document.querySelectorAll('#orderTable tbody tr');
    console.log('Total rows in table:', mainRows.length);
    
    mainRows.forEach((row, index) => {
        if (index < 3) { // Only first 3 rows
            const orderCodeDate = row.querySelector('.order-code-date')?.textContent.trim();
            const createdAt = row.querySelector('td:nth-child(3) small')?.textContent.trim();
            const paymentStatusElements = row.querySelectorAll('.status-pill');
            const paymentStatus = paymentStatusElements[0]?.textContent.trim();
            const reconciledStatus = paymentStatusElements[1]?.textContent.trim();
            
            console.log(`Row ${index}:`, {
                orderCodeDate,
                createdAt,
                paymentStatus,
                reconciledStatus,
                display: row.style.display || 'visible'
            });
        }
    });
    
    // Check DataTable search functions
    console.log('DataTable search functions:', $.fn.dataTable.ext.search.length);
};

// Test specific filter
window.testFilter = function(filterType, filterValue) {
    console.log(`🧪 Testing ${filterType} filter with value: ${filterValue}`);
    
    if (filterType === 'reconciled') {
        document.getElementById('reconciledFilter').value = filterValue;
        
        // Apply filter using our new function
        if (typeof applyMainFilters === 'function') {
            applyMainFilters();
        } else {
            $('#applyFilters').click();
        }
        
        setTimeout(() => {
            const visibleRows = $('#orderTable tbody tr:visible');
            console.log(`Found ${visibleRows.length} visible rows`);
            
            visibleRows.each(function(index) {
                if (index < 3) {
                    const reconciledStatus = $(this).find('.status-pill').eq(1).text().trim();
                    console.log(`Visible row ${index}: ${reconciledStatus}`);
                }
            });
        }, 200);
    } else if (filterType === 'payment') {
        document.getElementById('paymentFilter').value = filterValue;
        
        if (typeof applyMainFilters === 'function') {
            applyMainFilters();
        } else {
            $('#applyFilters').click();
        }
        
        setTimeout(() => {
            const visibleRows = $('#orderTable tbody tr:visible');
            console.log(`Found ${visibleRows.length} visible rows`);
            
            visibleRows.each(function(index) {
                if (index < 3) {
                    const paymentStatus = $(this).find('.status-pill').first().text().trim();
                    console.log(`Visible row ${index}: ${paymentStatus}`);
                }
            });
        }, 200);
    }
};

// Reset all filters
window.resetAllFilters = function() {
    console.log('🔄 Resetting all filters...');
    
    // Clear main table filters
    $('#dateFilter').val('');
    $('#paymentFilter').val('');
    $('#reconciledFilter').val('');
    $('#customSearch').val('');
    $('#orderTable tbody tr').show();
    
    // Clear shop table filters
    $('[id^="dateFilter"]').not('#dateFilter').val('');
    $('[id^="paymentFilter"]').not('#paymentFilter').val('');
    $('[id^="reconciledFilter"]').not('#reconciledFilter').val('');
    $('[id^="customSearch"]').not('#customSearch').val('');
    $('[id^="orderTableSHOP"] tbody tr').show();
    
    // Hide all no-data messages
    $('#noDataMessage').hide();
    $('[id^="noDataMessageShop"]').hide();
    
    // Show all mobile cards
    $('.mobile-order-card').show();
    
    console.log('✅ All filters reset and all rows shown');
};

// Call debug function after page load
$(document).ready(function() {
    setTimeout(() => {
        window.debugFiltering();
        
        // Quick test function
        window.quickTest = function() {
            console.log('🚀 Quick filter test - Reconciled status');
            window.testFilter('reconciled', 'Đã đối soát');
            
            setTimeout(() => {
                console.log('--- Switching to "Chưa đối soát" ---');
                window.testFilter('reconciled', 'Chưa đối soát');
            }, 1500);
            
            setTimeout(() => {
                console.log('--- Testing payment filter ---');
                window.testFilter('payment', 'Đã thanh toán');
            }, 3000);
        };
        
        console.log('💡 Available debug functions (Updated for simple filtering):');
        console.log('- window.debugFiltering() - Shows current filter states');
        console.log('- window.testFilter(type, value) - Test specific filters');
        console.log('  * testFilter("reconciled", "Đã đối soát")');
        console.log('  * testFilter("payment", "Đã thanh toán")');
        console.log('- window.resetAllFilters() - Reset all filters');
        console.log('- window.quickTest() - Run automated filter tests');
        
        // Show mobile filter toggle on mobile devices only
        if ($(window).width() <= 768) {
            $('#mobileFilterToggle').show();
            console.log('📱 Mobile filter toggle shown for mobile device');
        }
        
        // Handle resize
        $(window).on('resize', function() {
            if ($(window).width() <= 768) {
                $('#mobileFilterToggle').show();
            } else {
                $('#mobileFilterToggle').hide();
                if (typeof hideMobileFilter === 'function') {
                    hideMobileFilter(); // Use the proper hide function
                }
            }
        });
        
        // Test mobile filter functionality
        window.testMobileFilter = function() {
            console.log('🧪 Testing mobile filter');
            if ($(window).width() <= 768) {
                if (typeof showMobileFilter === 'function') {
                    showMobileFilter();
                } else {
                    console.error('showMobileFilter function not found');
                }
            } else {
                console.log('Not on mobile device');
            }
        };
    }, 2000);
});
</script>

@include('order.components.order-modal', ['order' => null])

<script>
    // Color coding for shop IDs
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId;
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>

@endsection