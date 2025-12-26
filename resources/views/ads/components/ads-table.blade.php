{{-- Component: Ads Table --}}
<div class="d-none d-md-block">
    <div class="modern-table-container">
        <div class="table-wrapper">
            <table id="adsTable" class="modern-table table">
                <thead class="table-header">
                    <tr>
                        <th>Mã Hóa đơn</th>
                        <th>Shop</th>
                        <th>Ngày Chi</th>
                        <th>Số Tiền</th>
                        <th>TAX (10%)</th>
                        <th>Tổng Cộng</th>
                        <th>Thanh Toán</th>
                        <th>Mã Thanh Toán</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @if($shopName)
                        {{-- Single shop view --}}
                        @foreach($ads as $ad)
                        <tr>
                            <td class="invoice-code-cell">
                                <div class="invoice-code-main">
                                    <span class="hienthicopy" data-invoice-code="{{$ad['invoice_id']}}">
                                        {{$ad['invoice_id']}}
                                        <span class="ri-clipboard-line icon" data-clipboard="{{$ad['invoice_id']}}"></span>
                                    </span>
                                </div>
                              
                            </td>
                            <td class="shop-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-store shop-platform-icon"></i>
                                    <span class="shop-name">{{ $shopName }}</span>
                                </div>
                            </td>
                            <td class="date-cell">{{$ad['date_range']}}</td>
                            <td class="amount-cell">{{ number_format($ad['amount'], 0, ',', '.') }} đ</td>
                            <td class="amount-cell">{{ number_format($ad['vat'], 0, ',', '.') }} đ</td>
                            <td class="amount-cell">{{ number_format($ad['total_amount'] ?? 0, 0, ',', '.') }} đ</td>
                            <td>
                                @if($ad['payment_status'] == 'Chưa thanh toán')
                                    <span class="status-pill status-unpaid">{{ $ad['payment_status'] }}</span>
                                @else
                                    <span class="status-pill status-paid">{{ $ad['payment_status'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="payment-code">{{$ad['payment_code']}}</span>
                            </td>
                            <td class="text-muted">
                                <small>{{$ad['created_at']}}</small>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        {{-- All ads view --}}
                        @foreach($ads as $ad)
                        <tr>
                            <td class="invoice-code-cell">
                                <div class="invoice-code-main">
                                    <span class="hienthicopy" data-invoice-code="{{$ad['invoice_id']}}">
                                        {{$ad['invoice_id']}}
                                        <span class="ri-clipboard-line icon" data-clipboard="{{$ad['invoice_id']}}"></span>
                                    </span>
                                </div>
                                <div class="invoice-code-date">{{$ad['date_range']}}</div>
                            </td>
                            <td class="shop-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-store shop-platform-icon"></i>
                                    <span class="shop-name">{{ $ad['shop_name'] ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="date-cell">{{$ad['date_range']}}</td>
                            <td class="amount-cell">{{ number_format($ad['amount'], 0, ',', '.') }} đ</td>
                            <td class="amount-cell">{{ number_format($ad['vat'], 0, ',', '.') }} đ</td>
                            <td class="amount-cell">{{ number_format($ad['total_amount'] ?? 0, 0, ',', '.') }} đ</td>
                            <td>
                                @if($ad['payment_status'] == 'Chưa thanh toán')
                                    <span class="status-pill status-unpaid">{{ $ad['payment_status'] }}</span>
                                @else
                                    <span class="status-pill status-paid">{{ $ad['payment_status'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="payment-code">{{$ad['payment_code']}}</span>
                            </td>
                            <td class="text-muted">
                                <small>{{$ad['created_at']}}</small>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <!-- No Data Message for Main Table -->
        <div class="no-data-message text-center py-5" id="noDataMessage" style="display: none;">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <i class="ri-search-line text-muted mb-3" style="font-size: 48px;"></i>
                <h5 class="text-muted mb-2">Không tìm thấy dữ liệu</h5>
                <p class="text-muted mb-0">Không có quảng cáo nào phù hợp với bộ lọc hiện tại</p>
                <button class="btn btn-outline-primary mt-3" onclick="$('#clearFilters').click();">
                    <i class="ri-refresh-line me-1"></i>Xóa bộ lọc
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Mobile Card --}}
@include('ads.components.ads-mobile-card', ['ads' => $ads, 'shopName' => $shopName ?? null])
<div id="mobile-pagination" class="d-block d-md-none"></div> 