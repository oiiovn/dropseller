window.initNapTienModalEvents = function() {
    const input = document.getElementById("soTien");
    const soTienError = document.getElementById("soTienError");
    const btnContinue = document.getElementById("generateQrButton");
    const noiDungChuyenKhoanInput = document.getElementById("noiDungChuyenKhoan");
    if (!input || !soTienError || !btnContinue || !noiDungChuyenKhoanInput) return;

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
        // Lấy nội dung chuyển khoản từ input hidden
        const addInfo = encodeURIComponent(noiDungChuyenKhoanInput.value.trim());
        const bankAccount = "62886838888";
        const accountName = "BUI QUOC VU";
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
        document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        document.body.classList.remove('modal-open');
    });
} 