{{-- Component: Order Table --}}
<link rel="stylesheet" href="{{ asset('assets/css/order-page.css') }}">
<div class="modern-table-container">
    <div class="table-wrapper">
        <table id="orderTable" class="modern-table table" >
            <thead class="table-header">
                <tr>
                    <th>Mã đơn nhập hàng</th>
                    <th>Shop</th>
                    <th>Ngày tạo đơn</th>
                    <th>Số lượng</th>
                    <th>Phí drop</th>
                    <th>Tổng Bill</th>
                    <th>Thanh toán</th>
                    <th>Mã thanh toán</th>
                    <th>Đối soát</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @foreach($orders as $item)
                @php
                    // Format filter_date to show only the first date
                    $displayDate = $item->filter_date;
                    if (strpos($item->filter_date, ' - ') !== false) {
                        $dateParts = explode(' - ', $item->filter_date);
                        $displayDate = $dateParts[0];
                    }
                @endphp
                <tr>
                    <td class="order-code-cell">
                        <div class="order-code-main">
                            <span class="order-link" data-order-code="{{$item->order_code}}">
                                {{$item->order_code}}
                                <span class="ri-clipboard-line icon"></span>
                            </span>
                        </div>
                        <div class="order-code-date">{{$displayDate}}</div>
                    </td>
                    <td class="customer_cost" data-shop-id="{{ $item->shop->shop_id ?? 0 }}">
                        <div class="d-flex align-items-center gap-2">
                            @if($item->shop->platform == 'Tiktok')
                                <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" loading="lazy" alt="" style="width: 24px; height: 24px; border-radius: 4px;">
                            @elseif($item->shop->platform == 'Shoppe')
                                <img src="https://img.icons8.com/fluency/240/shopee.png" loading="lazy" alt="" style="width: 24px; height: 24px; border-radius: 4px;">
                            @endif
                            <span class="shop-name">{{ $item->shop->shop_name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="text-muted">
                        <small>{{$item->created_at->format('d/m/Y H:i')}}</small>
                    </td>
                    <td class="quantity-cell">{{$item->total_products}}</td>
                    <td class="amount-cell">{{ number_format($item->total_dropship, 0, ',', '.') }} đ</td>
                    <td class="amount-cell">{{ number_format($item->total_bill, 0, ',', '.') }} đ</td>
                    <td>
                        @if($item->payment_status == 'Chưa thanh toán')
                            <span class="status-pill status-unpaid">{{ $item->payment_status }}</span>
                        @else
                            <span class="status-pill status-paid">{{ $item->payment_status }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="transaction-id">{{$item->transaction_id}}</span>
                    </td>
                    <td>
                        @if($item->reconciled == 1)
                            <span class="status-pill status-not-reconciled">Chưa đối soát</span>
                        @elseif($item->reconciled == 0)
                            <span class="status-pill status-reconciled">Đã đối soát</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="action-btn view-order-btn" 
                               data-order-id="{{$item->id}}"
                               data-order-code="{{$item->order_code}}"
                               data-shop-name="{{ $item->shop->shop_name ?? 'N/A' }}"
                               data-filter-date="{{$item->filter_date}}"
                               data-total-products="{{$item->total_products}}"
                               data-total-dropship="{{$item->total_dropship}}"
                               data-total-bill="{{$item->total_bill}}"
                               data-order-details='@json($item->orderDetails->toArray())'>
                            <i class="ri-eye-fill"></i>
                            <span>Chi tiết</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- No Data Message for Main Table -->
    <div class="no-data-message text-center py-5" id="noDataMessage" style="display: none;">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
            <h5 class="text-muted mb-2">Không tìm thấy dữ liệu</h5>
            <p class="text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc hiện tại</p>
            <button class="btn btn-outline-primary mt-3" onclick="$('#clearFilters').click();">
                <i class="ri-refresh-line me-1"></i>Xóa bộ lọc
            </button>
        </div>
    </div>
</div> 