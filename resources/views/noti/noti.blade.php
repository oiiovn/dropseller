
@if (isset($orders_unpaid) && !$orders_unpaid->isEmpty())
<a href="{{route('order_si')}}" class="alert-link"> 
<div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show material-shadow" role="alert">
    <i class="ri-error-warning-line label-icon"></i><strong style="margin-left:45px;">Số dư thấp</strong> - 
            <strong>
                Bạn đang có đơn hàng
                @foreach ($orders_unpaid as $order)
                {{ $order->order_code }} ,
                @endforeach
                đang bị trễ thanh toán! Để đươc sử dụng lại dịch vui lòng nạp tiền để đơn hàng của bạn được đóng gói!
            </strong>
    
</div>
</a>
@endif
@unless (Request::is('chien-dich'))
    <div class="alert alert-success alert-dismissible alert-additional fade show mb-0 material-shadow" role="alert">
        <div class="alert-body">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <i class="ri-alert-line fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading">Ra mắt tính năng tính toán phần trăm chiến dịch</h5>
                    <p class="mb-0">Nhằm đảm bảo lợi nhuận khi đăng kí chiến dịch TikTok Shop</p>
                </div>
            </div>
        </div>
        <a href="{{ route('campaign') }}">
            <div class="alert-content">
                <p class="mb-0">Xem Ngay Công Thức Tính Chiến Dịch</p>
            </div>
        </a>
    </div>
@endunless


<!-- Primary Alert
 <div class="alert alert-light alert-border-left alert-dismissible fade show material-shadow" style="border-left: 4px solid #000; margin-top:5px; color: darkslategray;"role="alert">
    <i class="ri-error-warning-line me-3 align-middle"></i> <strong>Thanh toán</strong> - Left border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>  -->
