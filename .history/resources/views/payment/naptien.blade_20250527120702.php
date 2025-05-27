<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal QR Example</title>
    <style>
        .modal-backdrop.show {
            background-color: rgba(0, 0, 0, 0.9);
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
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center" style="height: 47px; padding-top:3px;">
                    <h5 class="modal-title" id="napTienModalLabel">Thêm số dư vào tài khoản dropseller</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="soTien" class="form-label">Số tiền</label>
                            @if (isset($orders_unpaid) && $orders_unpaid->isNotEmpty())
                            @php
                            $total_bill = $orders_unpaid->sum('total_bill');
                            @if(
                            @endphp
                            <input type="text" class="form-control" id="soTien" placeholder="Số tiền ít nhất phải nạp {{ number_format($total_bill, 0, ',', '.') }} VNĐ" />
                            @else
                            <input type="text" class="form-control" id="soTien" placeholder="Nhập số tiền" />
                            @endif

                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex align-items-center" style="height: 57px; padding:5px;">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                    <button id="generateQrButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrModal">Thực hiện thanh toán</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hiển Thị QR -->
    <div class="modal fade" id="qrModal" aria-hidden="true" aria-labelledby="qrModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center" style="height: 37px; padding-top:3px;">
                    <h5 class="modal-title" id="qrModalLabel">QR Code Thanh Toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center" style="min-height: 205px; position: relative; overflow: hidden;">
                    <div class="col-7 alert material-shadow text-opacity-100" role="alert" style="height: 205px; overflow-wrap: break-word; margin-right:5px;">
                        <strong>Chào bạn! Quét mã QR để thanh toán!</strong> Số tiền sẽ được chuyển vào số dư của bạn trong 3-5 giây.
                        <b>Chuyển thành công</b> — Nhấn đóng để thoát!
                    </div>
                    <div class="qr-container col-5 rounded-4" style="width: 205px; height: 205px; border: 2px solid rgb(9, 61, 202); box-shadow: 0 0 10px rgb(47, 127, 232); position: relative; overflow: hidden;">
                        <div class="spinner-border text-primary" role="status" id="spinner">
                            <span class="visually-hidden">Đang tạo QR...</span>
                        </div>
                        <img src="" id="qrCode" class="d-none" alt="QR Code" style="width: 100%; height: 100%; border-radius: inherit; object-fit: contain;" />
                        <div class="glow-line"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center" style="height: 57px; padding:5px;">
                    <button class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->

    <script>
        document.getElementById("generateQrButton").addEventListener("click", function() {
            let soTien = input.value.replace(/[^0-9]/g, "");
            if (!soTien || parseInt(soTien) <= 0) {
                alert("Vui lòng nhập số tiền hợp lệ!");
                return;
            }
            input.value = new Intl.NumberFormat("vi-VN").format(soTien) + " VND";
            console.log("Referral Code:", referralCode);
            const bankAccount = "008338298888";
            const accountName = "BUI QUOC VU";
            const addInfo = encodeURIComponent(`${referralCode}`);
            const qrUrl = `https://img.vietqr.io/image/mbbank-${bankAccount}-200x200.png?amount=${soTien}&addInfo=${addInfo}&accountName=${encodeURIComponent(accountName)}`;
            console.log("Generated QR URL:", qrUrl);
            const qrImage = document.getElementById("qrCode");
            const spinner = document.getElementById("spinner");
            spinner.classList.remove("d-none");
            qrImage.classList.add("d-none");
            qrImage.src = qrUrl;
            qrImage.onload = () => {
                spinner.classList.add("d-none");
                qrImage.classList.remove("d-none");
            };
        });

        const input = document.getElementById("soTien");
        input.addEventListener("input", function(e) {
            const value = e.target.value.replace(/[^0-9]/g, "");
            const formattedValue = new Intl.NumberFormat("vi-VN").format(value)
            e.target.value = formattedValue;
        });
    </script>
</body>

</html>