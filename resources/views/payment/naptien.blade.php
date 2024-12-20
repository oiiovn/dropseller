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
            background-color: rgba(0, 0, 0, 0.3);
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
            height: 5px;
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
                <div class="modal-header">
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
                <div class="modal-footer d-flex align-items-center" style="height: 47px; border: 1px solid #ccc; ">
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
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code Thanh Toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center"
                    style="height: 100%; min-height: 205px; position: relative; overflow: hidden;">
                    <div class="qr-container" style="width: 205px; height: 205px; border-radius: 15px; border: 2px solid rgb(9, 61, 202); box-shadow: 0 0 10px rgb(47, 127, 232); position: relative; overflow: hidden;">
                        <img src="https://img.vietqr.io/image/mbbank-0939279388-200x200.png?amount=1889500&addInfo=LOVI120233&accountName=BUI%20THI%20DIEM%20VAN"
                            alt="QR Code" style="width: 100%; height: 100%; border-radius: inherit; object-fit: contain;" />
                        <div class="glow-line"></div>
                    </div>
                </div>


                <div class="modal-footer" d-flex align-items-center" style="height: 47px; border: 1px solid #ccc; ">
                    <button class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Example Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    This is an example modal to illustrate additional content.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modal Reset Example</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>

        <!-- Modal Nhập Số Tiền -->
        <div class="modal fade" id="napTienModal" aria-hidden="true" aria-labelledby="napTienModalLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="napTienModalLabel">Thêm số dư vào tài khoản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="soTien" class="form-label">Số tiền</label>
                                <input type="number" class="form-control" id="soTien" placeholder="Nhập số tiền" />
                            </div>
                            <div class="mb-3">
                                <label for="phiQuangCao" class="form-label">Phí quảng cáo</label>
                                <input type="text" class="form-control" id="phiQuangCao" value="0 VND" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tongSoTien" class="form-label">Tổng số tiền</label>
                                <input type="text" class="form-control" id="tongSoTien" value="0 VND" readonly />
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary" data-bs-target="#qrModal" data-bs-toggle="modal" data-bs-dismiss="modal">Thực hiện thanh toán</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hiển Thị QR -->
        <div class="modal fade" id="qrModal" aria-hidden="true" aria-labelledby="qrModalLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">QR Code Thanh Toán</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Quét mã QR để hoàn tất thanh toán:</p>
                        <img src="your-qr-code-url" alt="QR Code" class="img-fluid" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Example Modal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        This is an example modal to illustrate additional content.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Hàm reset modal "napTienModal"
                function resetNapTienModal() {
                    document.getElementById('soTien').value = '';
                    document.getElementById('phiQuangCao').value = '0 VND';
                    document.getElementById('tongSoTien').value = '0 VND';
                }

                // Đặt lại giao diện khi modal "qrModal" đóng
                const qrModal = document.getElementById('qrModal');
                qrModal.addEventListener('hidden.bs.modal', () => {
                    location.reload(); // Tải lại trang khi modal 2 đóng
                });

                // Đặt lại modal khi modal "napTienModal" đóng
                const napTienModal = document.getElementById('napTienModal');
                napTienModal.addEventListener('hidden.bs.modal', resetNapTienModal);
            });
            const qrModal = document.getElementById('qrModal');
            qrModal.addEventListener('hidden.bs.modal', () => {
    location.reload(); // Tải lại trang khi modal 2 đóng
});
// Đếm số lượng lớp phủ hiện tại
const overlays = document.querySelectorAll('.modal-backdrop');
console.log(`Số lượng lớp phủ: ${overlays.length}`);


        </script>

        </script>
    </body>

    </html>