@extends('layout')
@section('title', 'main')

@section('main')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Nhập chi tiêu quảng cáo</h4>
                    <hr class="border-top border-top-dashed border-success" style="border-top-width: 2px;">
                    <form id="adExpenseForm" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label for="shopSelect" class="form-label">Chọn Shop</label>
                            <select id="shopSelect" name="shop_id" class="form-select" required>
                                <option value="">Chọn Shop...</option>
                                @foreach($shops as $shop)
                                <option value="{{ $shop->shop_id }}">{{ $shop->shop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Ngày Bắt Đầu</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" required>
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">Ngày Kết Thúc</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" required>
                        </div>
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Số Tiền Chi Tiêu</label>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="Nhập số tiền" required>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" id="previewExpense">Cập Nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="adExpenseModal" tabindex="-1" aria-labelledby="adExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{route('add.ads')}}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="adExpenseModalLabel">Hoá Đơn Quảng Cáo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <hr class="border-top border-top-dashed border-success" style="border-top-width: 2px;">
                    <p><strong>Mã hoá đơn:</strong> <span id="modalInvoiceId"></span></p>
                    <p><strong>Shop:</strong> <span id="modalShopName"></span></p>
                    <p><strong>Ngày chi:</strong> <span id="modalStartDate"></span> - <span id="modalEndDate"></span></p>
                    <p><strong>Số Tiền Chi Tiêu:</strong> <span id="modalAmount"></span></p>
                    <p><strong>VAT (5%):</strong> <span id="modalVAT"></span></p>
                    <p><strong>Tổng cộng:</strong> <span id="modalTotal"></span></p>
                    <p><strong>Ngày tạo phiếu:</strong> <span id="modalCreatedDate"></span></p>

                    <!-- Hidden Inputs để gửi dữ liệu về backend -->
                    <input type="hidden" name="invoice_id" id="hiddenInvoiceId">
                    <input type="hidden" name="shop_id" id="hiddenShopId">
                    <input type="hidden" name="start_date" id="hiddenStartDate">
                    <input type="hidden" name="end_date" id="hiddenEndDate">
                    <input type="hidden" name="amount" id="hiddenAmount">
                    <input type="hidden" name="vat" id="hiddenVAT">
                    <input type="hidden" name="total" id="hiddenTotal">
                    <input type="hidden" name="created_date" id="hiddenCreatedDate">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-success">Tạo phiếu thu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('previewExpense').addEventListener('click', function() {
    const shopSelect = document.getElementById('shopSelect');
    const shopId = shopSelect.value;
    const shopName = shopSelect.selectedOptions[0].text;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const amountInput = document.getElementById('amount').value.replace(/[^0-9]/g, '');
    const amount = parseFloat(amountInput);

    // Kiểm tra nếu chưa chọn shop
    if (!shopId) {
        alert("Vui lòng chọn Shop!");
        return;
    }

    // Kiểm tra nếu chưa chọn ngày bắt đầu hoặc kết thúc
    if (!startDate || !endDate) {
        alert("Vui lòng chọn ngày bắt đầu và ngày kết thúc!");
        return;
    }

    // Kiểm tra nếu ngày kết thúc trước ngày bắt đầu
    if (new Date(endDate) < new Date(startDate)) {
        alert("Ngày kết thúc không thể trước ngày bắt đầu!");
        return;
    }

    // Kiểm tra nếu chưa nhập số tiền
    if (!amount || isNaN(amount) || amount <= 0) {
        alert("Vui lòng nhập số tiền hợp lệ!");
        return;
    }

    // Tính toán VAT và tổng tiền
    const vat = amount * 0.05;
    const total = amount + vat;
    const now = new Date();
    const createdDate = now.toLocaleDateString('vi-VN') + ' ' + now.toLocaleTimeString('vi-VN');

    // Cập nhật modal hiển thị
    document.getElementById('modalInvoiceId').textContent = 'QC' + now.getTime();
    document.getElementById('modalShopName').textContent = shopName;
    document.getElementById('modalStartDate').textContent = startDate;
    document.getElementById('modalEndDate').textContent = endDate;
    document.getElementById('modalAmount').textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    document.getElementById('modalVAT').textContent = new Intl.NumberFormat('vi-VN').format(vat) + ' đ';
    document.getElementById('modalTotal').textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    document.getElementById('modalCreatedDate').textContent = createdDate;

    // Gán giá trị vào hidden input để gửi về server
    document.getElementById('hiddenInvoiceId').value = 'QC' + now.getTime();
    document.getElementById('hiddenShopId').value = shopId;
    document.getElementById('hiddenStartDate').value = startDate;
    document.getElementById('hiddenEndDate').value = endDate;
    document.getElementById('hiddenAmount').value = amount;
    document.getElementById('hiddenVAT').value = vat;
    document.getElementById('hiddenTotal').value = total;
    document.getElementById('hiddenCreatedDate').value = createdDate;

    // Hiển thị modal
    let modal = new bootstrap.Modal(document.getElementById('adExpenseModal'));
    modal.show();
});

document.getElementById('amount').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9]/g, '');
    e.target.value = new Intl.NumberFormat('vi-VN').format(value) + ' đ';
});

</script>

@endsection
