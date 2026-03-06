<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal QR Example</title>
    <style>
       
        /* Đường phát sáng */
        .qr-container .glow-line {
            position: absolute;
            top: -10px;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, rgba(47, 127, 232, 0), rgba(47, 127, 232, 1), rgba(47, 127, 232, 0));
            box-shadow: 0 0 10px rgba(47, 127, 232, 0.8);
            animation: move-down 2s linear infinite;
        }
        .qr-display {
            display: flex;
        }
        @media (min-width: 768px) {
           
        }
        @keyframes move-down {
            0% {
                top: -10px;
            }

            100% {
                top: 100%;
            }
        }
        @media (max-width: 768px) {
            .qr-display {
                display: block;
            }
        }
    </style>
        <script>
        const referralCode = "{{ $referralCode }}";
    </script>
</head>

<body>
    <!-- Modal Nhập Số Tiền -->
    <div class="modal fade" id="napTienModal" aria-hidden="true" aria-labelledby="napTienModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 rounded-top-5 border-0">
                <div class="modal-header text-white rounded-top-4 border-0" style="padding-bottom: 20px; background-color: #0089ED;">
                    <h5 class="modal-title text-white fw-bold" id="napTienModalLabel">Nạp tiền vào tài khoản</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $noDon = $pending_orders_total ?? 0;
                        $noAds = $pending_ads_total ?? 0;
                        $minNap = $pending_payment_total ?? 0;
                    @endphp
                    @if($noDon > 0 || $noAds > 0 || $minNap > 0)
                    <div class="alert alert-warning border-warning mb-3 py-2 small">
                        <div class="fw-bold mb-2">Các khoản chưa thanh toán:</div>
                        <div class="d-flex justify-content-between"><span>Nợ đơn hàng:</span><span class="fw-bold">{{ number_format($noDon, 0, ',', '.') }} VNĐ</span></div>
                        <div class="d-flex justify-content-between"><span>Nợ quảng cáo:</span><span class="fw-bold">{{ number_format($noAds, 0, ',', '.') }} VNĐ</span></div>
                        <div class="d-flex justify-content-between mt-1 pt-1 border-top border-warning"><span>Số tiền nạp tối thiểu (giới hạn):</span><span class="fw-bold text-danger">{{ number_format($minNap, 0, ',', '.') }} VNĐ</span></div>
                    </div>
                    @endif
                    <form id="formNapTien">
                        <div class="mb-3">
                            <label for="soTien" class="form-label">Số tiền <span class="text-danger">*</span></label>
                            @php
                                $minDeposit = $pending_payment_total ?? 0;
                            @endphp
                            <input type="text" class="form-control form-control-lg" id="soTien"
                                value="{{ request('amount') ? number_format(request('amount'), 0, ',', '.') : '' }}"
                                placeholder="{{ $minDeposit > 0 ? 'Số tiền ít nhất phải nạp ' . number_format($minDeposit, 0, ',', '.') . ' VNĐ cho các khoản chưa thanh toán' : 'Nhập số tiền' }}"
                                autocomplete="off" />
                            <div class="invalid-feedback" id="soTienError">
                                Vui lòng nhập số tiền hợp lệ (tối thiểu {{ number_format($minDeposit > 0 ? $minDeposit : 10000, 0, ',', '.') }} VNĐ{{ $minDeposit > 0 ? ' cho các khoản chưa thanh toán' : '' }} )!
                            </div>
                        </div>
                        
                        <input type="hidden" id="noiDungChuyenKhoan" value="{{ $referralCode ?? '' }}" />
                    </form>
                </div>
                <div class="modal-footer d-flex align-items-end" style="height: 60px; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                    <button id="generateQrButton" class="btn px-4" disabled style="background-color: #0089ED; color: white;">Tiếp tục &rarr;</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hiển Thị QR -->
    <div class="modal fade" id="qrModal" aria-hidden="true" aria-labelledby="qrModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header text-white rounded-top-4" style="height: 56px; background-color: #0089ED;">
                    <h5 class="modal-title fw-bold text-white" id="qrModalLabel">QR Code Thanh Toán</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-3" style="min-height: 400px;">
                    <div class="alert alert-info w-100 text-center mb-2">
                        <strong>Quét mã QR để thanh toán!</strong><br/>
                        Số tiền sẽ được chuyển vào số dư của bạn trong 3-5 giây.<br/>
                        <b>Chuyển thành công</b> — Nhấn đóng để thoát!
                    </div>
                    
                    <!-- Thông tin chuyển khoản và QR -->
                    <div class="w-100">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0 fw-bold text-primary">Thông tin chuyển khoản</h6>
                            </div>
                            <div class="card-body">
                                <div class="qr-display justify-content-between">
                                    <!-- Thông tin bên trái -->
                                    <div class="mb-3 mb-md-0">
                                        <div class="">
                                            <div class="d-flex gap-2">
                                                <small class="text-muted">Ngân hàng:</small>
                                                <div class="fw-bold text-success">ACB</div>
                                            </div>
                                            
                                            <div class="d-flex gap-2 mt-2">
                                                <small class="text-muted">Tài khoản nhận:</small>
                                                <div class="fw-bold">PHATLOC934584939</div>
                                            </div>
                                            <div class="d-flex gap-2 mt-2">
                                                <small class="text-muted">Tên người nhận:</small>
                                                <div class="fw-bold">OIIO VN </div>
                                            </div>
                                            <div class="d-flex gap-2 mt-2">
                                                <small class="text-muted">Số tiền:</small>
                                                <div class="fw-bold text-danger" id="displayAmount"></div>
                                            </div>
                                            <div class="d-flex gap-2 mt-2">
                                                <small class="text-muted">Nội dung chuyển khoản:</small>
                                                <div class="fw-bold text-primary" id="displayContent">{{ $referralCode ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- QR bên phải (desktop) / dưới (mobile) -->
                                    <div class="d-flex justify-content-center">
                                        <div class="qr-container rounded-4 d-flex align-items-center justify-content-center position-relative" style="width: 180px; height: 180px; border: 2px solid #198754; box-shadow: 0 0 16px #19875455;">
                                            <div class="spinner-border text-success position-absolute top-50 start-50 translate-middle" role="status" id="spinner">
                                                <span class="visually-hidden">Đang tạo QR...</span>
                                            </div>
                                            <img src="" id="qrCode" class="d-none rounded-4" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center" style="height: 60px;">
                    <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hoàn tất</button>
                    <button id="captureQrButton" class="btn btn-success px-4">Chụp mã QR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{ asset('assets/js/naptien.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        // Khởi tạo sự kiện khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            initNapTienModalEvents();
        });
    </script>
</body>

</html>