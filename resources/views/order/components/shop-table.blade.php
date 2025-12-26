{{-- Component: Shop Table --}}
<div class="modern-table-container">
    <div class="table-wrapper">
        <table class="modern-table table" id="orderTableSHOP{{$shop->shop_id}}" >
            <thead class="table-header">
                <tr>
                    <th>Mã đơn nhập hàng</th>
                    <th>Ngày tạo</th>
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
                @foreach($orders->where('shop_id', $shop->shop_id) as $order)
                @php
                    // Format filter_date to show only the first date
                    $displayDate = $order->filter_date;
                    if (strpos($order->filter_date, ' - ') !== false) {
                        $dateParts = explode(' - ', $order->filter_date);
                        $displayDate = $dateParts[0];
                    }
                @endphp
                <tr>
                    <td class="order-code-cell">
                        <div class="order-code-main hienthicopy">
                            <span class="order-link" data-order-code="{{$order->order_code}}">
                                {{$order->order_code}}
                                <span class="ri-clipboard-line icon"></span>
                            </span>
                        </div>
                        <div class="order-code-date">{{$displayDate}}</div>
                    </td>
                    <td class="text-muted">
                        <small>{{$order->created_at->format('d/m/Y H:i')}}</small>
                    </td>
                    <td class="quantity-cell">{{$order->total_products}}</td>
                    <td class="amount-cell">{{ number_format($order->total_dropship, 0, ',', '.') }} đ</td>
                    <td class="amount-cell">{{ number_format($order->total_bill, 0, ',', '.') }} đ</td>
                    <td>
                        @if($order->payment_status == 'Chưa thanh toán')
                            <span class="status-pill status-unpaid">{{ $order->payment_status }}</span>
                        @else
                            <span class="status-pill status-paid">{{ $order->payment_status }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="transaction-id hienthicopy">
                            <span class="order-link" data-order-code="{{$order->transaction_id}}">
                                {{$order->transaction_id}}
                                <span class="ri-clipboard-line icon"></span>
                            </span>
                        </span>
                    </td>
                    <td>
                        @if($order->reconciled == 1)
                            <span class="status-pill status-not-reconciled">Chưa đối soát</span>
                        @elseif($order->reconciled == 0)
                            <span class="status-pill status-reconciled">Đã đối soát</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="action-btn view-order-btn" 
                               data-order-id="{{$order->id}}"
                               data-order-code="{{$order->order_code}}"
                               data-shop-name="{{ $order->shop->shop_name ?? 'N/A' }}"
                               data-filter-date="{{$order->filter_date}}"
                               data-total-products="{{$order->total_products}}"
                               data-total-dropship="{{$order->total_dropship}}"
                               data-total-bill="{{$order->total_bill}}"
                               data-order-details='@json($order->orderDetails->toArray())'>
                            <i class="ri-eye-fill"></i>
                            <span>Chi tiết</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- No Data Message for Shop Table -->
    <div class="no-data-message text-center py-5" id="noDataMessageShop{{$shop->shop_id}}" style="display: none;">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
            <h5 class="text-muted mb-2">Không tìm thấy dữ liệu</h5>
            <p class="text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc hiện tại</p>
            <button class="btn btn-outline-primary mt-3" onclick="$('#clearFilters{{$shop->shop_id}}').click();">
                <i class="ri-refresh-line me-1"></i>Xóa bộ lọc
            </button>
        </div>
    </div>
</div> 