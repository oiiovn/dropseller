<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Reset Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-backdrop.show {
            background-color: rgba(0, 0, 0, 0.9);
            /* Độ trong suốt cao hơn */

        }
    </style>
    <style>
        /* Đường phát sáng */
        .qr-container .glow-line {
            position: absolute;
            top: -10px;
            /* Bắt đầu ở ngoài container */
            left: 0;
            width: 100%;
            height: 10px;
            /* Độ dày của đường phát sáng */
            background: linear-gradient(to right, rgba(47, 127, 232, 0), rgba(47, 127, 232, 1), rgba(47, 127, 232, 0));
            box-shadow: 0 0 10px rgba(47, 127, 232, 0.8);
            animation: move-down 2s linear infinite;
        }

        /* Hiệu ứng chạy từ trên xuống */
        @keyframes move-down {
            0% {
                top: -10px;
            }

            100% {
                top: 100%;
                /* Kết thúc ở ngoài container */
            }
        }
    </style>
</head>

<body>

    <!-- Modal Nhập Số Tiền -->
    <div class="modal fade" id="napTienModal" aria-hidden="true" aria-labelledby="napTienModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center" style="height: 47px; padding-top:3px; ">
                    <h5 class="modal-title" id="napTienModalLabel">Thêm số dư vào tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3 ">
                            <label for="soTien" class="form-label">Số tiền</label>
                            <input type="number" class="form-control" id="soTien" placeholder="Nhập số tiền" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex align-items-center" style="height: 57px; padding:5px; ">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary" data-bs-target="#qrModal" data-bs-toggle="modal" data-bs-dismiss="modal">Thực hiện thanh toán</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hiển Thị QR -->
    <div class="modal fade" id="qrModal" aria-hidden="true" aria-labelledby="qrModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center" style="height: 47px; padding-top:3px; ">
                    <h5 class="modal-title" id="qrModalLabel">QR Code Thanh Toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center"
                    style="height: 100%; min-height: 205px; position: relative; overflow: hidden;">
                    <!-- success Alert -->
                    <div class="col-7 alert material-shadow text-opacity-100 " role="alert" style="height: 205px ; overflow-wrap: break-word; margin-right:5px;">
                        <strong> Chào bạn! Quét mã QR để thanh toán! </strong> Số tiền sẽ được chuyển vào số dư của bạn trong 3-5 giây <b>Chuyển thành công</b> — Nhấn đóng để thoát! <br> 

                    </div>

                    <div class="qr-container col-5 rounded-4 " style="width: 205px; height: 205px;  border: 2px solid rgb(9, 61, 202); box-shadow: 0 0 10px rgb(47, 127, 232); position: relative; overflow: hidden;">
                        <div class="spinner-border text-primary" role="status" id="spinner">
                            <span class="visually-hidden">Đang tạo QR...</span>
                        </div>
                        <img src="https://img.vietqr.io/image/mbbank-0939279388-200x200.png?amount=1889500&addInfo=LOVI120233&accountName=BUI%20THI%20DIEM%20VAN"
                            id="qrCode" class="d-none" alt="QR Code" style="width: 100%; height: 100%; border-radius: inherit; object-fit: contain;" />
                        <div class="glow-line"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center" style="height: 57px; padding:5px; ">
                    <button class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const qrImage = document.getElementById("qrCode");
        qrImage.onload = () => {
            document.getElementById("spinner").classList.add("d-none");
            qrImage.classList.remove("d-none");
        };
    </script>
</body>

</html>