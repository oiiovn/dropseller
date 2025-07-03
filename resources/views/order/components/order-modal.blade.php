{{-- Component: Order Detail Modal --}}
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-1 px-4">
                <h5 class="modal-title mb-0 fw-bold hienthicopy" id="orderDetailModalLabel" data-order-code="{{$order->order_code}}">
                    {{$order->order_code}}
                    <span class="ri-clipboard-line icon" style="cursor:pointer;" onclick="navigator.clipboard.writeText('{{$order->order_code}}');"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                
             

                <!-- Order Details Table -->
                <div class=" shadow-sm border-0">
                   
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-hover align-middle mb-0">

                                <tbody id="modal-order-details">
                                    <!-- Order details will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center p-4" id="modal-loading" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="mt-2 mb-0 text-muted">Đang tải chi tiết...</p>
                        </div>
                        <div class="text-center p-4" id="modal-no-data" style="display: none;">
                            <i class="ri-information-line fs-2 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">Không có dữ liệu chi tiết đơn hàng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 