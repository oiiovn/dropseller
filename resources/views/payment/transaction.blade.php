@extends('layout')
@section('title', 'main')

@section('main')
<style>
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .stats-title {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .stats-value {
        color: #344767;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .bg-gradient-primary {
        background: linear-gradient(310deg, #2152ff, #21d4fd);
    }
    .bg-gradient-success {
        background: linear-gradient(310deg, #17ad37, #98ec2d);
    }
    .bg-gradient-danger {
        background: linear-gradient(310deg, #ea0606, #ff667c);
    }
    .bg-gradient-warning {
        background: linear-gradient(310deg, #f53939, #fbcf33);
    }
    .nav-pills .nav-link {
        color: #344767;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #459fff 0%, #a93232 100%);
        color: white;
    }
    .table thead th {
        font-weight: 600;
        color: #344767;
    }
    .table tbody td {
        vertical-align: middle;
    }
    .badge-border {
        border: 1px solid;
        padding: 5px 12px;
    }

    /* Table Styles */
    .table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    .table td {
        padding: 16px;
        vertical-align: middle;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }

    /* Status Badge Styles */
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .badge-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    /* Amount Styles */
    .amount-in {
        color: #16a34a;
        font-weight: 600;
    }

    .amount-out {
        color: #dc2626;
        font-weight: 600;
    }

    /* Tab Styles */
    .nav-pills {
        background: white;
        padding: 8px;
        border-radius: 10px;
        margin-bottom: 24px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .nav-pills .nav-link {
        padding: 12px 24px;
        color: #64748b;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-pills .nav-link:hover {
        color: #334155;
        background: #f1f5f9;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #459fff 0%, #a93232 100%);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .nav-pills .nav-link i {
        margin-right: 8px;
    }

    /* Pagination Styles */
    .pagination {
        margin: 20px 0;
        gap: 5px;
    }

    .page-link {
        border: none;
        padding: 8px 16px;
        color: #64748b;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #459fff 0%, #a93232 100%);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Search and Filter Styles */
    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 16px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #459fff;
        box-shadow: 0 0 0 3px rgba(69, 159, 255, 0.1);
    }

    .btn-filter {
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-filter:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }

    /* Responsive Table */
    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table td {
            white-space: nowrap;
        }
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 10px;
            padding: 15px;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }

        .stats-title {
            font-size: 12px;
        }

        .stats-value {
            font-size: 18px;
        }

        .nav-pills {
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-bottom: 15px;
        }

        .nav-pills .nav-item {
            width: calc(50% - 4px); /* 2 tabs per row with gap */
        }

        .nav-pills .nav-link {
            padding: 8px 12px;
            font-size: 13px;
            width: 100%;
            text-align: center;
            white-space: normal;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-pills .nav-link i {
            font-size: 14px;
            margin-right: 4px;
        }

        /* Table Responsive Improvements */
        .table-responsive {
            margin: 0 -12px;  /* Negative margin to stretch table */
            padding: 0 12px;
            overflow-x: auto;
        }

        .table {
            min-width: 800px; /* Ensure minimum width for content */
        }

        .table thead th {
            padding: 12px 8px;
            font-size: 11px;
        }

        .table td {
            padding: 12px 8px;
            font-size: 13px;
        }

        /* Custom scrollbar for better mobile experience */
        .table-responsive::-webkit-scrollbar {
            height: 4px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        /* Mobile Filter Section */
        .filter-section {
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-section .form-group {
            width: 100%;
        }

        .filter-section .btn {
            width: 100%;
            margin-top: 5px;
        }

        /* Mobile Pagination */
        .dataTables_paginate {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .paginate_button {
            padding: 5px 10px !important;
            font-size: 12px;
        }

        /* Mobile Search */
        .dataTables_filter {
            width: 100%;
            margin-bottom: 10px;
        }

        .dataTables_filter input {
            width: 100% !important;
            margin: 0 !important;
        }

        /* Mobile Length Menu */
        .dataTables_length {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
        }

        .dataTables_length select {
            width: auto;
            padding: 5px 25px 5px 10px;
        }

        /* Badge Adjustments */
        .badge {
            padding: 4px 8px;
            font-size: 11px;
        }

        /* Amount Display */
        .amount-in, .amount-out {
            font-size: 13px;
        }

        /* Mobile Card Optimization */
        @media (max-width: 768px) {
            .stats-card {
                padding: 6px;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                min-height: auto;
            }

            .stats-icon {
                width: 24px;
                height: 24px;
                margin-bottom: 0;
                margin-right: 6px;
            }

            .stats-icon i {
                font-size: 16px !important;
            }

            .stats-content {
                flex: 1;
            }

            .stats-title {
                font-size: 12px;
                margin-bottom: 2px;
                color: #64748b;
            }

            .stats-value {
                font-size: 14px;
                margin-bottom: 0;
                line-height: 1.2;
            }

            /* Adjust grid columns for mobile */
            .col-md-6 {
                padding-left: 6px;
                padding-right: 6px;
            }

            .row {
                margin-left: -6px;
                margin-right: -6px;
            }

            /* Container padding adjustment */
            .container-fluid {
                padding: 10px;
            }
        }
    </style>

    @php
        $balance = 0;
        $total_in = 0;
        $total_out = 0;
        $total_ads = 0;

        foreach ($Transactions as $transaction) {
            if ($transaction->type === 'IN') {
                $balance += $transaction->amount;
                $total_in += $transaction->amount;
            } elseif ($transaction->type === 'OUT') {
                $balance -= $transaction->amount;
                $total_out += $transaction->amount;
                
                if ($transaction->bank === 'ADS') {
                    $total_ads += $transaction->amount;
                }
            }
        }
    @endphp

    <div class="container-fluid bg-light" style="min-height: 84vh;">
        <div class="row mt-2">
            <!-- Số dư hiện tại -->
            <div class="col-xl-3 col-md-6 col-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, rgba(37,99,235,0.1) 0%, rgba(37,99,235,0.2) 100%);">
                        <i class="ri-wallet-3-line" style="font-size: 24px; color: #2563eb;"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-title">Số dư hiện tại</div>
                        <div class="stats-value">{{ number_format($balance) }} VNĐ</div>
                    </div>
                </div>
            </div>

            <!-- Tổng nạp -->
            <div class="col-xl-3 col-md-6 col-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, rgba(22,163,74,0.1) 0%, rgba(22,163,74,0.2) 100%);">
                        <i class="ri-arrow-up-circle-line" style="font-size: 24px; color: #16a34a;"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-title">Tổng nạp</div>
                        <div class="stats-value">{{ number_format($total_in) }} VNĐ</div>
                    </div>
                </div>
            </div>

            <!-- Tổng chi -->
            <div class="col-xl-3 col-md-6 col-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, rgba(220,38,38,0.1) 0%, rgba(220,38,38,0.2) 100%);">
                        <i class="ri-arrow-down-circle-line" style="font-size: 24px; color: #dc2626;"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-title">Tổng chi</div>
                        <div class="stats-value">{{ number_format($total_out) }} VNĐ</div>
                    </div>
                </div>
            </div>

            <!-- Chi tiêu quảng cáo -->
            <div class="col-xl-3 col-md-6 col-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, rgba(234,179,8,0.1) 0%, rgba(234,179,8,0.2) 100%);">
                        <i class="ri-megaphone-line" style="font-size: 24px; color: #eab308;"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-title">Chi tiêu quảng cáo</div>
                        <div class="stats-value">{{ number_format($total_ads) }} VNĐ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active mx-2" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">
                            <i class="ri-exchange-funds-line me-1"></i>Tất cả giao dịch
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mx-2" id="pills-bill-si-tab" data-bs-toggle="pill" data-bs-target="#pills-bill-si" type="button" role="tab" aria-controls="pills-bill-si" aria-selected="false">
                            <i class="ri-shopping-cart-line me-1"></i>Giao dịch đơn sỉ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mx-2" id="pills-nap-tab" data-bs-toggle="pill" data-bs-target="#pills-nap" type="button" role="tab" aria-controls="pills-nap" aria-selected="false">
                            <i class="ri-money-dollar-circle-line me-1"></i>Nạp tiền
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mx-2" id="pills-ads-tab" data-bs-toggle="pill" data-bs-target="#pills-ads" type="button" role="tab" aria-controls="pills-ads" aria-selected="false">
                            <i class="ri-advertisement-line me-1"></i>Chi tiêu ADS
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="pills-tabContent">
                    <!-- Tất cả giao dịch -->
                    <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                        <div class="table-responsive">
                            <table style=" width: 100%;" class="table table-nowrap " id="all">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID giao dịch</th>
                                        <!-- <th scope="col">Ngân hàng</th>
                                        <th scope="col">Số tài khoản</th> -->
                                        <th scope="col">Nội dung</th>
                                        <th scope="col">Ngày</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Transactions as $Transaction)
                                    <tr>
                                        <td>
                                            {{$Transaction->transaction_id}}
                                        </td>
                                        <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                        <td>{{$Transaction->account_number}}</td> -->
                                        <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{$Transaction->description}}
                                        </td>
                                        <td>{{$Transaction->transaction_date}}</td>
                                        <td>
                                            @if ($Transaction->type === 'IN')
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @elseif ($Transaction->type === 'OUT')
                                            <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span>
                                                @if ($Transaction->type === 'IN')
                                                <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                                @elseif ($Transaction->type === 'OUT')
                                                <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                                @else
                                                <span>Unknown Type</span>
                                                @endif
                                            </span>
                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $('#all').DataTable({
                                        "paging": true, // Bật phân trang
                                        "searching": true, // Bật tìm kiếm
                                        "ordering": true, // Bật sắp xếp
                                        "info": true, // Hiển thị thông tin
                                        "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                        "order": [
                                            [2, "desc"]
                                        ], // Sắp xếp theo cột thứ 3 (Ngày giao dịch) theo ngày mới nhất (desc)

                                        // Chỉnh Tiếng Việt
                                        "language": {
                                            "lengthMenu": "",
                                            "zeroRecords": "Không tìm thấy dữ liệu",
                                            "info": "",
                                            "infoEmpty": "Không có dữ liệu để hiển thị",
                                            "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                            "search": "Tìm kiếm:",
                                            "paginate": {
                                                "first": "Trang đầu",
                                                "last": "Trang cuối",
                                                "next": ">",
                                                "previous": "<"
                                            }
                                        }
                                    });
                                });
                            </script>

                        </div>
                    </div>
                    <!-- Giao dịch đơn sỉ -->
                    <div class="tab-pane fade" id="pills-bill-si" role="tabpanel" aria-labelledby="pills-bill-si-tab">
                        <div class="table-responsive">
                            <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="bill-si">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID giao dịch</th>
                                        <!-- <th scope="col">Ngân hàng</th>
                                        <th scope="col">Số tài khoản</th> -->
                                        <th scope="col">Nội dung</th>
                                        <th scope="col">Ngày</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Transactions_Drop as $Transaction)
                                    <tr>
                                        <td>
                                            {{$Transaction->transaction_id}}
                                        </td>
                                        <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                        <td>{{$Transaction->account_number}}</td> -->
                                        <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{$Transaction->description}}
                                        </td>
                                        <td>{{$Transaction->transaction_date}}</td>
                                        <td>
                                            @if ($Transaction->type === 'IN')
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @elseif ($Transaction->type === 'OUT')
                                            <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span>
                                                @if ($Transaction->type === 'IN')
                                                <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                                @elseif ($Transaction->type === 'OUT')
                                                <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                                @else
                                                <span>Unknown Type</span>
                                                @endif
                                            </span>
                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $('#bill-si').DataTable({
                                        "paging": true, // Bật phân trang
                                        "searching": true, // Bật tìm kiếm
                                        "ordering": true, // Bật sắp xếp
                                        "info": true, // Hiển thị thông tin
                                        "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                        "order": [
                                            [2, "desc"]
                                        ], // Sắp xếp theo cột thứ 3 (Ngày giao dịch) theo ngày mới nhất (desc)

                                        // Chỉnh Tiếng Việt
                                        "language": {
                                            "lengthMenu": "",
                                            "zeroRecords": "Không tìm thấy dữ liệu",
                                            "info": "",
                                            "infoEmpty": "Không có dữ liệu để hiển thị",
                                            "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                            "search": "Tìm kiếm:",
                                            "paginate": {
                                                "first": "Trang đầu",
                                                "last": "Trang cuối",
                                                "next": ">",
                                                "previous": "<"
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>
                    </div>
                    <!-- Nạp tiền -->
                    <div class="tab-pane fade" id="pills-nap" role="tabpanel" aria-labelledby="pills-nap-tab">
                        <div class="table-responsive">
                            <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="nap">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID giao dịch</th>
                                        <!-- <th scope="col">Ngân hàng</th>
                                        <th scope="col">Số tài khoản</th> -->
                                        <th scope="col">Nội dung</th>
                                        <th scope="col">Ngày</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Transaction_nap as $Transaction)
                                    <tr>
                                        <td>
                                            {{$Transaction->transaction_id}}
                                        </td>
                                        <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                        <td>{{$Transaction->account_number}}</td> -->
                                        <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{$Transaction->description}}
                                        </td>
                                        <td>{{$Transaction->transaction_date}}</td>
                                        <td>
                                            @if ($Transaction->type === 'IN')
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @elseif ($Transaction->type === 'OUT')
                                            <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span>
                                                @if ($Transaction->type === 'IN')
                                                <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                                @elseif ($Transaction->type === 'OUT')
                                                <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                                @else
                                                <span>Unknown Type</span>
                                                @endif
                                            </span>
                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $('#nap').DataTable({
                                        "paging": true, // Bật phân trang
                                        "searching": true, // Bật tìm kiếm
                                        "ordering": true, // Bật sắp xếp
                                        "info": true, // Hiển thị thông tin
                                        "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                        "order": [
                                            [2, "desc"]
                                        ], // Sắp xếp theo cột thứ 3 (Ngày giao dịch) theo ngày mới nhất (desc)


                                        // Chỉnh Tiếng Việt
                                        "language": {
                                            "lengthMenu": "",
                                            "zeroRecords": "Không tìm thấy dữ liệu",
                                            "info": "",
                                            "infoEmpty": "Không có dữ liệu để hiển thị",
                                            "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                            "search": "Tìm kiếm:",
                                            "paginate": {
                                                "first": "Trang đầu",
                                                "last": "Trang cuối",
                                                "next": ">",
                                                "previous": "<"
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>
                    </div>
                    <!-- Chi tiêu ADS -->
                    <div class="tab-pane fade" id="pills-ads" role="tabpanel" aria-labelledby="pills-ads-tab">
                        <div class="table-responsive">
                            <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="ADS">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID giao dịch</th>
                                        <!-- <th scope="col">Ngân hàng</th>
                                        <th scope="col">Số tài khoản</th> -->
                                        <th scope="col">Nội dung</th>
                                        <th scope="col">Ngày</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Transactions_ads as $Transaction)
                                    <tr>
                                        <td>
                                            {{$Transaction->transaction_id}}
                                        </td>
                                        <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                        <td>{{$Transaction->account_number}}</td> -->
                                        <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{$Transaction->description}}
                                        </td>
                                        <td>{{$Transaction->transaction_date}}</td>
                                        <td>
                                            @if ($Transaction->type === 'IN')
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @elseif ($Transaction->type === 'OUT')
                                            <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span>
                                                @if ($Transaction->type === 'IN')
                                                <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                                @elseif ($Transaction->type === 'OUT')
                                                <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                                @else
                                                <span>Unknown Type</span>
                                                @endif
                                            </span>
                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $('#ADS').DataTable({
                                        "paging": true, // Bật phân trang
                                        "searching": true, // Bật tìm kiếm
                                        "ordering": true, // Bật sắp xếp
                                        "info": true, // Hiển thị thông tin
                                        "lengthMenu": [10, 20, 50, 100, 200], // Số lượng dòng hiển thị
                                        "order": [
                                            [2, "desc"]
                                        ], // Sắp xếp theo cột thứ 3 (Ngày giao dịch) theo ngày mới nhất (desc)


                                        // Chỉnh Tiếng Việt
                                        "language": {
                                            "lengthMenu": "",
                                            "zeroRecords": "Không tìm thấy dữ liệu",
                                            "info": "",
                                            "infoEmpty": "Không có dữ liệu để hiển thị",
                                            "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                            "search": "Tìm kiếm:",
                                            "paginate": {
                                                "first": "Trang đầu",
                                                "last": "Trang cuối",
                                                "next": ">",
                                                    "previous": "<"
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>
                    </div>
                    <!-- Hoá đơn dịch vụ -->
                    <div class="tab-pane fade" id="pills-dich-vu" role="tabpanel" aria-labelledby="pills-dich-vu-tab">
                        <div class="table-responsive">
                            <table style="table-layout: fixed; width: 100%;" class="table table-nowrap ">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ID giao dịch</th>
                                        <!-- <th scope="col">Ngân hàng</th>
                                        <th scope="col">Số tài khoản</th> -->
                                        <th style="width: 350px;">Nội dung</th>
                                        <th scope="col">Ngày</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Transactions as $Transaction)
                                    <tr>
                                        <td>
                                            {{$Transaction->transaction_id}}
                                        </td>
                                        <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                        <td>{{$Transaction->account_number}}</td> -->
                                        <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{$Transaction->description}}
                                        </td>


                                        <td>{{$Transaction->transaction_date}}</td>
                                        <td><span class="badge bg-info">{{ number_format($Transaction->amount, 0, '.', ',') }}VNĐ</span></td>

                                        <td>
                                            {{$Transaction->type}}
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection