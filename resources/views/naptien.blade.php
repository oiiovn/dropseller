@extends('layout')
@section('title', 'Nạp Tiền')

@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <form id="naptienForm" class="d-flex align-items-center w-100" method="POST" action="{{ route('transaction.store') }}">
                        @csrf
                        <div class="form-group me-3 col-2">
                            <label for="userSelect">Chọn Người Dùng:</label>
                            <select class="form-control" id="userSelect" name="user">
                                @foreach ($users as $user)
                                <option value="{{ $user->referral_code }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group me-3 col-2">
                            <label for="amountInput">Số Tiền:</label>
                            <input type="text" class="form-control" id="amountInput" name="amount" placeholder="Nhập số tiền">
                        </div>
                        <input type="hidden" id="hiddenuser" name="referral_code">
                        <input type="hidden" id="hiddenAmount" name="Amount">
                        <button type="button" class="btn btn-info mt-auto" id="previewDeposit" data-bs-toggle="modal" data-bs-target="#confirmModal">Nạp</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác Nhận Nạp Tiền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Mã Người Dùng:</strong> <span id="modaluser"></span></p>
                <p><strong>Số Tiền:</strong> <span id="modalAmount"></span> </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('previewDeposit').addEventListener('click', function() {
        const userSelect = document.getElementById('userSelect');
        const user = userSelect.value;
        const amount = document.getElementById('amountInput').value.replace(/[^0-9]/g, '');

        if (!user) {
            alert("Vui lòng chọn Người Dùng!");
            return;
        }

        if (!amount || isNaN(amount) || amount <= 0) {
            alert("Vui lòng nhập số tiền hợp lệ!");
            return;
        }

        // Hiển thị trong modal
        document.getElementById('modaluser').textContent = user;
        document.getElementById('modalAmount').textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' đ';

        // Ẩn dữ liệu vào input hidden để gửi lên server
        document.getElementById('hiddenuser').value = user;
        document.getElementById('hiddenAmount').value = amount;
    });

    document.getElementById('confirmButton').addEventListener('click', function() {
        document.getElementById('naptienForm').submit();
    });

    document.getElementById('amountInput').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    });
</script>
@endsection
