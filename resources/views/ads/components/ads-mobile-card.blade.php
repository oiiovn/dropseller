{{-- Component: Ads Mobile Card --}}
<div class="mobile-card-container d-block d-md-none">
    @foreach($ads as $ad)
    <div class="mobile-ads-card mb-2">
        <div class="mobile-card-header d-flex justify-content-between">
            <span class="mobile-shop-name">{{ $ad['shop_name'] ?? $shopName ?? 'N/A' }}</span>
            
            <div class="mobile-invoice-code">
                <span class="text-right mobile-status-pill {{ $ad['payment_status'] == 'Chưa thanh toán' ? 'mobile-status-unpaid' : 'mobile-status-paid' }}">
                    {{ $ad['payment_status'] }}
                </span>
                {{ $ad['invoice_id'] }}
                <span class="ri-clipboard-line icon" data-clipboard="{{ $ad['invoice_id'] }}"></span>
            </div>
        </div>
        <div class="mobile-ads-info">
           
          
            <div class="mobile-ads-details">
                <div><b class="mobile-amount">Số tiền: <span style="font-size: 12px;">{{ number_format($ad['amount'], 0, ',', '.') }}</span> VNĐ</b> </div>
                <span style="font-size: 12px; text-align: right; color: #666;">{{ $ad['payment_code'] }}</span>
                <div><b class="mobile-vat">TAX (10%): {{ number_format($ad['vat'], 0, ',', '.') }} VNĐ</b></div>
                <div><b class="mobile-total" style="color: #666; font-weight: 500;">Tổng cộng: <span style="color: #009522; font-weight: 600; font-size: 14px;">{{ number_format($ad['total_amount'] ?? 0, 0, ',', '.') }}</span> VNĐ</b></div>
                
            </div>
          
            <div class="mobile-status-section">
            <div><b class="mobile-created-at">Ngày tạo: {{ $ad['created_at'] }}</b></div>
            <div class="mobile-invoice-date"><b class="mobile-vat">Ngày lọc: {{ $ad['date_range'] }}</b></div>
            </div>
        </div>
    </div>
    @endforeach
</div> 