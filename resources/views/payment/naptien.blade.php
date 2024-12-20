@extends('layout')
@section('title', 'main')

@section('main')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Nạp Tiền</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Nút Nạp Tiền -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#napTienModal">
    Nạp tiền
</button>

<!-- Modal (Popup) -->
<div class="modal fade" id="napTienModal" tabindex="-1" aria-labelledby="napTienLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="napTienLabel">Thêm số dư vào tài khoản của bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="soTien" class="form-label">Số tiền</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="soTien" placeholder="Nhập số tiền">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phí quảng cáo</label>
                        <p>0 VND</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Tổng số tiền</label>
                        <h5 class="fw-bold">0 VND</h5>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success">Thực hiện thanh toán</button>
            </div>
        </div>
    </div>
</div>

@endsection
