<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kẹp Giấy Hết Hàng </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('{{ asset('assets/images/bg-gift.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-flex {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 32px;
            padding-top: 20px;
            flex-wrap: wrap;
        }
        .gift-form-container {
            width: 340px;
            background: rgba(255,255,255,0.85);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            position: relative;
        }
        .gift-form-container .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.2rem;
        }
        .gift-form-container .logo-dot {
            width: 8px;
            height: 8px;
            background: #7c3aed;
            border-radius: 50%;
            display: inline-block;
        }
        .gift-form-container .logo-dot:nth-child(n+2) { margin-left: 2px; }
        .gift-form-container h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .form-label {
            font-weight: 500;
            color: #222;
        }
        .form-control, .form-select {
            background: #e9ecef;
            border: none;
            border-radius: 8px;
            margin-bottom: 1.1rem;
            padding: 0.7rem 1rem;
            font-size: 1rem;
        }
        .sku-limit {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 2rem;
        max-width: 200px;
        word-break: break-word;
        line-height: 1.2em;
        height: calc(1.2em * 3); /* Tương đương 3 dòng */
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 2px #7c3aed33;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(90deg, #7c3aed 0%, #6366f1 100%);
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #6366f1 0%, #7c3aed 100%);
        }
        .print-guide {
            color: #e11d48;
            font-size: 2rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
            background: #fff3f3;
            border-radius: 8px;
            padding: 0.5em 1em;
            border-left: 4px solid #e11d48;
        }
        /* Card bên phải */
        .gift-card-a6 {
            width: 150mm;
            height: 230mm;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 0.3cm 0.3cm 0.3cm 0.3cm;
           
            font-size: clamp(0.95rem, 1.08rem, 1.08rem);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
            overflow: hidden;
            transition: font-size 0.2s;
        }
        .gift-card-a6 .brand-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.5rem;
        }
        .gift-card-a6 .brand-row img.qr {
            width: 170px;
            height: 170px;
            border-radius: 8px;
            padding: 0.5rem;
            border: 2px solid #000;
            object-fit: cover;
        }
        .gift-card-a6 .brand-row .tiktok {
            font-size: 2rem;
            color: #000;
            font-weight: bold;
            margin-left: 6px;
        }
        .gift-card-a6 .shop-title {
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
        }
        .gift-card-a6 .sku {
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
            word-break: break-all;
        }
        .gift-card-a6 .sku span {
            color: #222;
        }
        .gift-card-a6 .money {
            color: #e11d48;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .gift-card-a6 .zalo {
            font-weight: 600;
            font-size: 1.5rem;
            margin-top: 1.1rem;
        }
        .gift-card-a6 .user-code {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #000;
            letter-spacing: 2px;
        }
        table{
            font-size: 2rem;
        }
        @media (max-width: 900px) {
            .main-flex { flex-direction: column; align-items: center; }
        }
        /* In A6 */
        @media print {
            html, body {
              
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            body * { visibility: hidden !important; }
            #printA6Card, #printA6Card * { visibility: visible !important; }
            #printA6Card {
                position: absolute;
                left: 0; top: 0;
                width: 100% !important;
                height: 100% !important;
                min-height: unset;
                box-shadow: none;
                margin: 0 !important;
                padding: 0cm 0.4cm !important;
                background: #fff !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
              
                page-break-inside: avoid !important;
                overflow: hidden !important;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
            }
            table{
                font-size: 2rem;
            }
            @page {
                size: A5 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="main-flex">
        <!-- FORM -->
        <div class="gift-form-container">
           
            <h5 class="text-uppercase text-dark mb-4" style="letter-spacing:1px; display: flex; justify-content: space-between; align-items: center;">
                <span id="titleText">Hết Hàng Kẹp Giấy</span>
                <i class="fas fa-exchange-alt" id="toggleIcon" style="cursor: pointer; color: #7c3aed; font-size: 1.2rem;" title="Đổi loại"></i>
            </h5>
           
            <form id="giftForm" autocomplete="off">
                <div class="mb-3">
                    <label for="shop" class="form-label">Chọn shop</label>
                    <select class="form-select" id="shop" required></select>
                </div>
                <div class="mb-3">
                    <label for="userCode" class="form-label">Mã đơn hàng</label>
                    <input type="text" class="form-control" id="userCode" placeholder="Nhập mã đơn hàng" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*">
                </div>
                <div class="mb-3">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control" id="sku" placeholder="Nhập SKU" required>
                </div>
                <div class="mb-3">
                    <label for="total" class="form-label">Tổng tiền</label>
                    <input type="number" class="form-control" id="total" placeholder="Nhập tổng tiền" required oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6).replace(/^0+/, '')" pattern="[0-9]*" maxlength="6">
                </div>
                <button type="button" class="btn btn-primary" onclick="printA6()">In giấy A6</button>
            </form>
        </div>
        <!-- CARD HIỂN THỊ -->
        <div class="gift-card-a6 " id="printA6Card">
           <div  style="border: 3px solid #000; padding: 0.5cm; height: 100%; border-radius: 20px;" >
           <div class="brand-row mb-2">
                <img id="cardQR" src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://tiktok.com/@brania.official" alt="QR" class="qr">
                <span class="tiktok"><span id="cardShop"></span></span>
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:inherit;">
            
                <tr>
                    <td style="font-weight:600; padding:0.2em 0; font-size:2rem;">Mã đơn:</td>
                    <td><span id="cardUserCode" style="font-size:2rem;"></span> (6 số cuối mã đơn hàng)</td>

                </tr>
                <tr>
                    <td style="font-weight:600; padding:0.2em 0; font-size:2rem;">SKU:</td>
                    <td><span id="cardSku" style="font-size:2rem;"></span></td>
                </tr>
                <tr>
                    <td style="font-weight:600; padding:0.2em 0; font-size:2rem;">SĐT/Zalo:</td>
                    <td><span id="cardZalo" style="font-size:2rem;"></span></td>
                </tr>
            </table>
                         <div style="margin-top:0.7em; font-size:0.98em; text-align:center; ">
                 <div class="shop-title" id="giftMessage" style="font-size:1.05em; font-weight:600; font-size:2rem; display:none;" >SHOP TẶNG BẠN SP NÀY</div>
                 <div style="font-size:0.97em; font-size:1.8rem;">(Vì lý do hết hàng!)</div>
                 <div style="margin-top:0.5em; font-size:2rem;">Khi nhận hàng bạn nhắn shop số tài khoản<br>Shop sẽ chuyển tiền lại cho bạn tổng là: <span class="money" id="cardTotal" style="font-size:2.2rem;"></span></div>
             </div>
           </div>
        </div>
    </div>
    <script>
        // Danh sách shop
        const danhSachShop = [
          { tenShop: "BRANIA", soDienThoai: "0934584939", anhQR: "" },
          { tenShop: "Lovito", soDienThoai: "0934584939", anhQR: "" },
          { tenShop: "DIVA HCM", soDienThoai: "0934584939", anhQR: "" },
          { tenShop: "BEVEDA", soDienThoai: "0939279388", anhQR: "" },
          { tenShop: "Kami House", soDienThoai: "0939279388", anhQR: "" },
          { tenShop: "Beveda Store", soDienThoai: "0939279388", anhQR: "" },
          { tenShop: "LIYURI STORE", soDienThoai: "0939279388", anhQR: "" },
          { tenShop: "Hanatruong94", soDienThoai: "0794599699", anhQR: "" },
          { tenShop: "Trinh Aura", soDienThoai: "0794599699", anhQR: "" },
          { tenShop: "Honava", soDienThoai: "0949790626", anhQR: "" },
          { tenShop: "Cao Vi Store", soDienThoai: "0888892379", anhQR: "" },
          { tenShop: "SooHi", soDienThoai: "0965610655", anhQR: "" },
          { tenShop: "Dani Closet", soDienThoai: "0363957495", anhQR: "" },
          { tenShop: "My Phương Châu", soDienThoai: "0989948178", anhQR: "" },
          { tenShop: "Mỹ Phương Shopee", soDienThoai: "0989948178", anhQR: "" },
          { tenShop: "Meew Store", soDienThoai: "0348952328", anhQR: "" },
          { tenShop: "Meew Shoppe", soDienThoai: "0348952328", anhQR: "" },
          { tenShop: "Nangvouge Store", soDienThoai: "0836137637", anhQR: "" },
          { tenShop: "Quế Phương Store", soDienThoai: "0967409590", anhQR: "" }
        ];
        // Render dropdown
        const shopSelect = document.getElementById('shop');
        shopSelect.innerHTML = '<option value="">-- Chọn shop --</option>' +
          danhSachShop.map((s, i) => `<option value="${i}">${s.tenShop}</option>`).join('');

        const userCode = document.getElementById('userCode');
        const skuInput = document.getElementById('sku');
        const totalInput = document.getElementById('total');
        const titleText = document.getElementById('titleText');
        const toggleIcon = document.getElementById('toggleIcon');
        // Card fields
        const cardShop = document.getElementById('cardShop');
        const cardSku = document.getElementById('cardSku');
        const cardTotal = document.getElementById('cardTotal');
        const cardZalo = document.getElementById('cardZalo');
        const cardQR = document.getElementById('cardQR');
        const cardUserCode = document.getElementById('cardUserCode');
        const cardA6 = document.getElementById('printA6Card');
        const giftMessage = document.getElementById('giftMessage');
        let currentShopIdx = "";
        let isGiftMode = true; // true = Tặng Áo Kẹp Giấy, false = Hết Hàng Kẹp Giấy
        // Khi chọn shop
        shopSelect.addEventListener('change', function() {
            currentShopIdx = shopSelect.value;
            updateQR();
            if (currentShopIdx === "") {
                cardShop.textContent = "BRANIA";
                cardZalo.textContent = "0939279388";
            } else {
                const shop = danhSachShop[currentShopIdx];
                cardShop.textContent = shop.tenShop;
                cardZalo.textContent = shop.soDienThoai;
            }
            autoFontSize();
        });
        skuInput.addEventListener('input', function() {
            cardSku.textContent = skuInput.value || '';
            autoFontSize();
        });
        totalInput.addEventListener('input', function() {
            cardTotal.textContent = totalInput.value ? formatMoney(totalInput.value) + ' đ' : '';
            var cardTotal2 = document.getElementById('cardTotal2');
            if(cardTotal2) cardTotal2.textContent = totalInput.value ? formatMoney(totalInput.value) + ' đ' : '';
            autoFontSize();
        });
        userCode.addEventListener('input', function() {
            let val = userCode.value.replace(/\D/g, '');
            let last6 = val.length > 6 ? val.slice(-6) : val;
            cardUserCode.textContent = last6 ? ` ${last6}` : '';
            updateQR();
            autoFontSize();
        });
        // Helper format tiền
        function formatMoney(val) {
            let n = val.replace(/[^\d]/g, '');
            return n.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        // Cập nhật QR code
        function updateQR() {
            let code = userCode.value.trim();
            if (code) {
                cardQR.src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(code)}`;
            } else if (currentShopIdx !== "") {
                const shop = danhSachShop[currentShopIdx];
                if (shop.anhQR && shop.anhQR.trim() !== "") {
                    cardQR.src = shop.anhQR;
                } else if (shop.soDienThoai && shop.soDienThoai.trim() !== "") {
                    cardQR.src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://zalo.me/${shop.soDienThoai}`;
                } else {
                    cardQR.src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(shop.tenShop)}`;
                }
            } else {
                cardQR.src = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://tiktok.com/@brania.official";
            }
        }
        // Tự động thu nhỏ font nếu nội dung quá dài
        function autoFontSize() {
            const maxHeight = cardA6.offsetHeight;
            let fontSize = 1.08; // rem
            cardA6.style.fontSize = fontSize + 'rem';
            let tries = 0;
            while (cardA6.scrollHeight > cardA6.offsetHeight && fontSize > 0.7 && tries < 10) {
                fontSize -= 0.04;
                cardA6.style.fontSize = fontSize + 'rem';
                tries++;
            }
        }
        // Toggle giữa Tặng Áo Kẹp Giấy và Hết Hàng Kẹp Giấy
        toggleIcon.addEventListener('click', function() {
            isGiftMode = !isGiftMode;
            if (isGiftMode) {
                titleText.textContent = 'Tặng Áo Kẹp Giấy';
                giftMessage.style.display = 'block';
            } else {
                titleText.textContent = 'Hết Hàng Kẹp Giấy';
                giftMessage.style.display = 'none';
            }
            autoFontSize();
        });

        // Gọi autoFontSize khi load
        window.addEventListener('DOMContentLoaded', function() {
            autoFontSize();
            updateQR();
            var cardTotal2 = document.getElementById('cardTotal2');
            if(cardTotal2) cardTotal2.textContent = totalInput.value ? formatMoney(totalInput.value) + ' đ' : '128,991 đ';
        });
        // In chỉ card A6
        function printA6() {
            autoFontSize();
            setTimeout(() => window.print(), 200);
        }
    </script>
</body>
</html> 