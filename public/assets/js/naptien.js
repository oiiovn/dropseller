window.initNapTienModalEvents = function() {
    const input = document.getElementById("soTien");
    const soTienError = document.getElementById("soTienError");
    const btnContinue = document.getElementById("generateQrButton");
    const noiDungChuyenKhoanInput = document.getElementById("noiDungChuyenKhoan");
    if (!input || !soTienError || !btnContinue || !noiDungChuyenKhoanInput) return;

    // Lấy số tiền tối thiểu từ placeholder nếu có, nếu không thì mặc định là 10000
    function getMinAmount() {
        const placeholder = input.placeholder || "";
        const match = placeholder.match(/([0-9.,]+)\s*VNĐ/);
        if (match) {
            // Loại bỏ dấu chấm, phẩy
            return parseInt(match[1].replace(/[^0-9]/g, ""), 10);
        }
        return 10000;
    }

    function validateSoTien() {
        const minAmount = getMinAmount();
        const value = input.value.replace(/[^0-9]/g, "");
        if (minAmount !== 10000) {
            soTienError.innerHTML = `Vui lòng nhập số tiền hợp lệ (tối thiểu ${minAmount.toLocaleString('vi-VN')} VNĐ cho các khoản chưa thanh toán)!`;
        } else {
            soTienError.textContent = `Vui lòng nhập số tiền hợp lệ (tối thiểu ${minAmount.toLocaleString('vi-VN')} đ)!`;
        }
        if (value && parseInt(value) >= minAmount) {
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
        const minAmount = getMinAmount();
        if (!soTien || parseInt(soTien) < minAmount) {
            soTienError.style.display = "block";
            input.classList.add("is-invalid");
            input.focus();
            return;
        }
        soTienError.style.display = "none";
        input.classList.remove("is-invalid");
        
        // Cập nhật thông tin hiển thị trong modal QR
        const displayAmount = document.getElementById("displayAmount");
        const displayContent = document.getElementById("displayContent");
        
        if (displayAmount) {
            displayAmount.textContent = new Intl.NumberFormat("vi-VN").format(soTien) + " VNĐ";
        }
        if (displayContent) {
            displayContent.textContent = noiDungChuyenKhoanInput.value.trim();
        }
        
        // Lấy nội dung chuyển khoản từ input hidden
        const addInfo = encodeURIComponent(noiDungChuyenKhoanInput.value.trim());
        const bankAccount = "PHATLOC934584939";
        const accountName = "OIIO.VN";
        const qrUrl = `https://img.vietqr.io/image/ACB-${bankAccount}-200x200.png?amount=${soTien}&addInfo=${addInfo}`;
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
        document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        document.body.classList.remove('modal-open');
    });
    // Xử lý nút chụp mã QR
    document.getElementById('captureQrButton').addEventListener('click', function() {
        const qrContainer = document.querySelector('.qr-container');
        if (!qrContainer) return;

        // Ẩn modal backdrop để không bị dính nền mờ
        document.querySelectorAll('.modal-backdrop').forEach(e => e.style.display = 'none');

        html2canvas(qrContainer, {
            backgroundColor: null,
            useCORS: true
        }).then(function(canvas) {
            // Chuyển canvas thành blob
            canvas.toBlob(function(blob) {
                // Tạo URL cho blob
                const blobUrl = URL.createObjectURL(blob);
                
                // Tạo thẻ a với thuộc tính để lưu vào thư viện ảnh
                const link = document.createElement('a');
                link.href = blobUrl;
                link.target = '_blank';
                link.rel = 'noopener';
                link.download = 'QR-Thanh-Toan-' + new Date().getTime() + '.png';
                
                // Thêm thuộc tính để mở trong thư viện ảnh (chỉ hoạt động trên một số trình duyệt)
                link.setAttribute('data-gallery', 'photo-library');
                
                // Click vào link để mở hộp thoại lưu/mở
                document.body.appendChild(link);
                link.click();
                
                // Dọn dẹp
                document.body.removeChild(link);
                URL.revokeObjectURL(blobUrl);
            }, 'image/png');

            // Hiện lại modal backdrop
            document.querySelectorAll('.modal-backdrop').forEach(e => e.style.display = '');
        });
    });
    
} 