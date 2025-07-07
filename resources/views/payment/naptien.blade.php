<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal QR Example</title>
    <style>
        .modal-backdrop.show {
            /* background-color: rgba(0, 0, 0, 0.9); */
        }

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

        @keyframes move-down {
            0% {
                top: -10px;
            }

            100% {
                top: 100%;
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
                    <form id="formNapTien">
                        <div class="mb-3">
                            <label for="soTien" class="form-label">Số tiền <span class="text-danger">*</span></label>
                            @if (isset($orders_unpaid) && $orders_unpaid->isNotEmpty())
                                @php
                                    $total_bill = $orders_unpaid->sum('total_bill');
                                @endphp
                                <input type="text" class="form-control form-control-lg" id="soTien" placeholder="Số tiền ít nhất phải nạp {{ number_format($total_bill, 0, ',', '.') }} VNĐ" autocomplete="off" />
                            @else
                                <input type="text" class="form-control form-control-lg" id="soTien"
                                    value="{{ request('amount') ? number_format(request('amount'), 0, ',', '.') : '' }}"
                                    placeholder="Nhập số tiền" autocomplete="off" />
                            @endif
                            <div class="invalid-feedback" id="soTienError">Vui lòng nhập số tiền hợp lệ (tối thiểu 10.000đ)!</div>
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
                <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-3" style="min-height: 260px;">
                    <div class="alert alert-info w-100 text-center mb-2">
                        <strong>Quét mã QR để thanh toán!</strong><br/>
                        Số tiền sẽ được chuyển vào số dư của bạn trong 3-5 giây.<br/>
                        <b>Chuyển thành công</b> — Nhấn đóng để thoát!
                    </div>
                    <div class="qr-container rounded-4 d-flex align-items-center justify-content-center position-relative" style="width: 210px; height: 210px; border: 2px solid #198754; box-shadow: 0 0 16px #19875455;">
                        <div class="spinner-border text-success position-absolute top-50 start-50 translate-middle" role="status" id="spinner">
                            <span class="visually-hidden">Đang tạo QR...</span>
                        </div>
                        <img src="" id="qrCode" class="d-none rounded-4" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center" style="height: 60px;">
                    <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hoàn tất</button>
                    <button class="btn btn-outline-danger px-4" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->

    <script>
        // Định dạng số tiền khi nhập
        const input = document.getElementById("soTien");
        const soTienError = document.getElementById("soTienError");
        const btnContinue = document.getElementById("generateQrButton");
        function validateSoTien() {
            const value = input.value.replace(/[^0-9]/g, "");
            if (value && parseInt(value) >= 10000) {
                btnContinue.disabled = false;
                btnContinue.classList.add("btn-primary");
                btnContinue.classList.remove("btn-secondary");
                soTienError.style.display = "none";
                input.classList.remove("is-invalid");
            } else {
                btnContinue.disabled = true;
                btnContinue.classList.remove("btn-primary");
                btnContinue.classList.add("btn-secondary");
                if (input.value !== "") {
                    soTienError.style.display = "block";
                    input.classList.add("is-invalid");
                } else {
                    soTienError.style.display = "none";
                    input.classList.remove("is-invalid");
                }
            }
        }
        input.addEventListener("input", function(e) {
            const value = e.target.value.replace(/[^0-9]/g, "");
            if (value) {
                e.target.value = new Intl.NumberFormat("vi-VN").format(value);
            } else {
                e.target.value = "";
            }
            validateSoTien();
        });
        // Khởi tạo trạng thái ban đầu
        validateSoTien();
        // Xử lý nút tiếp tục
        btnContinue.addEventListener("click", function(e) {
            e.preventDefault();
            let soTien = input.value.replace(/[^0-9]/g, "");
            if (!soTien || parseInt(soTien) < 10000) {
                soTienError.style.display = "block";
                input.classList.add("is-invalid");
                input.focus();
                return;
            }
            soTienError.style.display = "none";
            input.classList.remove("is-invalid");
            // Sinh QR
            const referralCode = "{{ $referralCode ?? '' }}";
            const bankAccount = "62886838888";
            const accountName = "BUI QUOC VU";
            const addInfo = encodeURIComponent(`${referralCode}`);
            const qrUrl = `https://img.vietqr.io/image/mbbank-${bankAccount}-200x200.png?amount=${soTien}&addInfo=${addInfo}`;
            const qrImage = document.getElementById("qrCode");
            const spinner = document.getElementById("spinner");
            spinner.classList.remove("d-none");
            qrImage.classList.add("d-none");
            qrImage.src = qrUrl;
            // Hiện modal QR
            const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
            qrModal.show();
            qrImage.onload = () => {
                spinner.classList.add("d-none");
                qrImage.classList.remove("d-none");
            };
        });
        // Đảm bảo modal QR reset khi đóng
        document.getElementById('qrModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById("qrCode").src = "";
            document.getElementById("spinner").classList.remove("d-none");
            document.getElementById("qrCode").classList.add("d-none");
        });
    </script>
</body>

</html>