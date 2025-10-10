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

    /* Mobile Card View Styles */
    .mobile-cards-container {
        display: flex;
        flex-direction: column;
    }
    
    .transaction-card {
        display: none;
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #459fff;
        transition: all 0.3s ease;
        order: unset; /* Đảm bảo thứ tự theo DOM */
    }

    .transaction-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.2), 0 4px 15px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    .transaction-card.type-in {
        border-left-color: #16a34a;
    }

    .transaction-card.type-out {
        border-left-color: #dc2626;
    }

    .card-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-transaction-id {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .card-type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }

    .card-type-badge.type-in {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .card-type-badge.type-out {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .card-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .card-info-label {
        color: #64748b;
        font-weight: 500;
    }

    .card-info-value {
        color: #1e293b;
        font-weight: 600;
        text-align: right;
        max-width: 60%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .card-amount {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
    }

    .card-amount-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    .card-amount-value {
        font-size: 18px;
        font-weight: 700;
    }

    .card-amount-value.positive {
        color: #16a34a;
    }

    .card-amount-value.negative {
        color: #dc2626;
    }

    .card-description {
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
        margin-top: 8px;
        word-break: break-word;
    }

    /* Mobile Search Styles - Ẩn mặc định */
    .mobile-search-container {
        margin-bottom: 15px;
        display: none;
    }
    
    /* Hiển thị mobile search trên tablet/mobile */
    @media (max-width: 1180px) {
        .row .mobile-search-container {
            display: block !important;
        }
        
        .row:has(.mobile-search-container) {
            display: flex !important;
        }
    }

    .mobile-search-container .input-group-text {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }

    .mobile-search-container .form-control {
        border-color: #e2e8f0;
        font-size: 14px;
    }

    .mobile-search-container .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    .mobile-search-container .btn-outline-secondary {
        border-color: #e2e8f0;
        color: #64748b;
    }

    .mobile-search-container .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #475569;
    }

    /* Hidden cards when searching */
    .transaction-card.hidden {
        display: none !important;
    }


    /* Tablet/iPad Optimization - iPad Air 5 có width 820px landscape */
    @media (max-width: 1180px) and (min-width: 769px) {
        /* Hide table, show cards */
        .table-responsive {
            display: none !important;
        }

        .transaction-card {
            display: block;
        }
        
        /* Hide desktop search, show mobile search */
        .dataTables_filter {
            display: none !important;
        }
        
        .mobile-search-container {
            display: block !important;
        }
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        /* Hide table, show cards */
        .table-responsive {
            display: none !important;
        }

        .transaction-card {
            display: block;
        }
        
        /* Hide desktop search, show mobile search */
        .dataTables_filter {
            display: none !important;
        }
        
        .mobile-search-container {
            display: block !important;
        }
    }

    /* Stats card styles */
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

    /* Modern Table Styles */
    .table-responsive {
        margin: 0 -12px;
        padding: 0 12px;
        overflow-x: auto;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        height: calc(100vh - 300px); /* Chiều cao tối ưu */
        min-height: 500px; /* Chiều cao tối thiểu */
        max-height: calc(100vh - 200px); /* Chiều cao tối đa */
    }

    .table {
        min-width: 900px;
        margin-bottom: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        height: 100%; /* Chiều cao 100% của container */
        table-layout: fixed; /* Cố định layout */
    }

    /* Tối ưu chiều cao cho DataTables */
    .dataTables_wrapper {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .dataTables_scroll {
        flex: 1;
        overflow-y: auto;
    }

    .dataTables_scrollBody {
        height: auto !important;
        max-height: calc(100vh - 350px) !important;
    }

    /* Tối ưu pagination */
    .dataTables_paginate {
        margin-top: 15px;
        padding: 10px 0;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .table thead th {
        padding: 16px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 16px 12px;
        font-size: 14px;
        color: #374151;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Modern Badge Styles */
    .badge-border {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        border: 1px solid transparent;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .badge.bg-secondary-subtle {
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%) !important;
        color: #475569 !important;
        border-color: #cbd5e1 !important;
    }

    .badge.bg-danger-subtle {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%) !important;
        color: #dc2626 !important;
        border-color: #fca5a5 !important;
    }

    /* Column Width Control */
    .table td:first-child,
    .table th:first-child {
        width: 180px !important;
        min-width: 180px;
        max-width: 180px;
    }

    .table td:nth-child(2),
    .table th:nth-child(2) {
        width: auto !important;
        min-width: 300px;
        max-width: 400px;
    }

    .table td:nth-child(3),
    .table th:nth-child(3) {
        width: 160px !important;
        min-width: 160px;
        max-width: 160px;
    }

    .table td:nth-child(4),
    .table th:nth-child(4) {
        width: 180px !important;
        min-width: 180px;
        max-width: 180px;
    }

    .table td:last-child,
    .table th:last-child {
        width: 120px !important;
        min-width: 120px;
        max-width: 120px;
    }

    /* Transaction ID styling */
    .table td:first-child {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #1f2937;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    }

    /* Description column */
    .table td:nth-child(2) {
        word-break: break-word;
        line-height: 1.4;
    }

    /* Date column */
    .table td:nth-child(3) {
        font-family: 'Courier New', monospace;
        color: #6b7280;
        white-space: nowrap;
    }

    /* Amount column */
    .table td:nth-child(4) {
        text-align: right;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Type column */
    .table td:last-child {
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    /* Additional modern styling */
    .transaction-id {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #1f2937;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 13px;
    }

    .transaction-description {
        max-width: 350px;
        word-break: break-word;
        line-height: 1.4;
        color: #374151;
    }

    .transaction-date {
        font-family: 'Courier New', monospace;
        color: #6b7280;
        font-size: 13px;
    }

    .transaction-type {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .transaction-type.type-in {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }

    .transaction-type.type-out {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .badge.bg-success-subtle {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%) !important;
        color: #166534 !important;
        border-color: #bbf7d0 !important;
    }

    /* Tối ưu layout tổng thể */
    .tab-content {
        height: calc(100vh - 200px);
        overflow: hidden;
    }

    .tab-pane {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .tab-pane.active {
        display: flex !important;
    }

    /* Tối ưu cho tablet/iPad (iPad Air 5 = 820px landscape) */
    @media (max-width: 1180px) and (min-width: 769px) {
        .tab-content {
            height: auto;
        }
        
        .table-responsive {
            height: auto;
            min-height: auto;
            max-height: none;
        }
        
        .dataTables_scrollBody {
            max-height: none !important;
        }
        
        /* Tối ưu cards cho tablet */
        .transaction-card {
            margin-bottom: 18px;
            padding: 24px;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15), 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card-header-row {
            margin-bottom: 18px;
            padding-bottom: 18px;
        }
        
        .card-transaction-id {
            font-size: 17px;
        }
        
        .card-type-badge {
            padding: 8px 18px;
            font-size: 14px;
        }
        
        .card-description {
            font-size: 16px;
            margin-top: 14px;
        }
        
        .card-amount-value {
            font-size: 22px;
        }
    }

    /* Tối ưu cho mobile */
    @media (max-width: 768px) {
        .tab-content {
            height: auto;
        }
        
        .table-responsive {
            height: auto;
            min-height: auto;
            max-height: none;
        }
        
        .dataTables_scrollBody {
            max-height: none !important;
        }
        
        .transaction-card {
            margin-bottom: 12px;
            padding: 16px;
        }
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mx-2" id="pills-dich-vu-tab" data-bs-toggle="pill" data-bs-target="#pills-dich-vu" type="button" role="tab" aria-controls="pills-dich-vu" aria-selected="false">
                            <i class="ri-service-line me-1"></i>Hóa đơn dịch vụ
                        </button>
                    </li>
                </ul>

                <!-- Mobile Search Bar (visible on tablet and mobile, hidden on desktop > 1180px) -->
                <div class="row mb-4" style="display: none;">
                    <div class="col-12">
                        <div class="mobile-search-container">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ri-search-line"></i>
                                </span>
                                <input type="text" id="mobileTransactionSearch" class="form-control" placeholder="Tìm kiếm giao dịch...">
                                <button class="btn btn-outline-secondary" type="button" id="mobileClearSearch">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Content -->
                <div class="tab-content" id="pills-tabContent">
                    <!-- Tất cả giao dịch -->
                    <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                        <!-- Mobile Cards View -->
                        <div class="mobile-cards-container">
                            @foreach($Transactions as $Transaction)
                            <div class="transaction-card {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                <!-- ID giao dịch -->
                                <div class="card-header-row">
                                    <div class="card-transaction-id">{{ $Transaction->transaction_id }}</div>
                                    <span class="card-type-badge {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                        {{ $Transaction->type === 'IN' ? 'Nạp số dư' : 'Chi số dư' }}
                                    </span>
                                </div>
                                <!-- Nội dung -->
                                <div class="card-description">
                                    <strong class="card-info-label">Nội dung:</strong><br>
                                    {{ $Transaction->description }}
                                </div>
                                <!-- Ngày -->
                                <div class="card-info-row">
                                    <span class="card-info-label">Ngày:</span>
                                    <span class="card-info-value">{{ $Transaction->transaction_date }}</span>
                                </div>
                                <!-- Tổng tiền -->
                                <div class="card-amount">
                                    <span class="card-amount-label">Tổng tiền:</span>
                                    <span class="card-amount-value {{ $Transaction->type === 'IN' ? 'positive' : 'negative' }}">
                                        {{ $Transaction->type === 'IN' ? '+' : '-' }}{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
                        <div class="table-responsive">
                            <table class="table" id="all">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <i class="ri-hashtag me-1"></i>ID giao dịch
                                        </th>
                                        <th scope="col">
                                            <i class="ri-file-text-line me-1"></i>Nội dung
                                        </th>
                                        <th scope="col">
                                            <i class="ri-calendar-line me-1"></i>Ngày giao dịch
                                        </th>
                                        <th scope="col">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Tổng tiền
                                        </th>
                                        <th scope="col">
                                            <i class="ri-exchange-line me-1"></i>Loại
                                        </th>
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
                        <!-- Mobile Cards View -->
                        <div class="mobile-cards-container">
                            @foreach($Bill_Si as $Transaction)
                            <div class="transaction-card {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                <div class="card-header-row">
                                    <div class="card-transaction-id">{{ $Transaction->transaction_id }}</div>
                                    <span class="card-type-badge {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                        {{ $Transaction->type === 'IN' ? 'Nạp số dư' : 'Chi số dư' }}
                                    </span>
                                </div>
                                <div class="card-description">
                                    <strong class="card-info-label">Nội dung:</strong><br>
                                    {{ $Transaction->description }}
                                </div>
                                <div class="card-info-row">
                                    <span class="card-info-label">Ngày:</span>
                                    <span class="card-info-value">{{ $Transaction->transaction_date }}</span>
                                </div>
                                <div class="card-amount">
                                    <span class="card-amount-label">Tổng tiền:</span>
                                    <span class="card-amount-value {{ $Transaction->type === 'IN' ? 'positive' : 'negative' }}">
                                        {{ $Transaction->type === 'IN' ? '+' : '-' }}{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
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
                        <!-- Mobile Cards View -->
                        <div class="mobile-cards-container">
                            @foreach($Naptien as $Transaction)
                            <div class="transaction-card type-in">
                                <div class="card-header-row">
                                    <div class="card-transaction-id">{{ $Transaction->transaction_id }}</div>
                                    <span class="card-type-badge type-in">Nạp số dư</span>
                                </div>
                                <div class="card-description">
                                    <strong class="card-info-label">Nội dung:</strong><br>
                                    {{ $Transaction->description }}
                                </div>
                                <div class="card-info-row">
                                    <span class="card-info-label">Ngày:</span>
                                    <span class="card-info-value">{{ $Transaction->transaction_date }}</span>
                                </div>
                                <div class="card-amount">
                                    <span class="card-amount-label">Tổng tiền:</span>
                                    <span class="card-amount-value positive">
                                        +{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
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
                        <!-- Mobile Cards View -->
                        <div class="mobile-cards-container">
                            @foreach($ADS as $Transaction)
                            <div class="transaction-card type-out">
                                <div class="card-header-row">
                                    <div class="card-transaction-id">{{ $Transaction->transaction_id }}</div>
                                    <span class="card-type-badge type-out">Chi tiêu ADS</span>
                                </div>
                                <div class="card-description">
                                    <strong class="card-info-label">Nội dung:</strong><br>
                                    {{ $Transaction->description }}
                                </div>
                                <div class="card-info-row">
                                    <span class="card-info-label">Ngày:</span>
                                    <span class="card-info-value">{{ $Transaction->transaction_date }}</span>
                                </div>
                                <div class="card-amount">
                                    <span class="card-amount-label">Tổng tiền:</span>
                                    <span class="card-amount-value negative">
                                        -{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
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
                        <!-- Mobile Cards View -->
                        <div class="mobile-cards-container">
                            @foreach($Dich_Vu as $Transaction)
                            <div class="transaction-card {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                <div class="card-header-row">
                                    <div class="card-transaction-id">{{ $Transaction->transaction_id }}</div>
                                    <span class="card-type-badge {{ $Transaction->type === 'IN' ? 'type-in' : 'type-out' }}">
                                        {{ $Transaction->type === 'IN' ? 'Nạp số dư' : 'Chi số dư' }}
                                    </span>
                                </div>
                                <div class="card-description">
                                    <strong class="card-info-label">Nội dung:</strong><br>
                                    {{ $Transaction->description }}
                                </div>
                                <div class="card-info-row">
                                    <span class="card-info-label">Ngày:</span>
                                    <span class="card-info-value">{{ $Transaction->transaction_date }}</span>
                                </div>
                                <div class="card-amount">
                                    <span class="card-amount-label">Tổng tiền:</span>
                                    <span class="card-amount-value {{ $Transaction->type === 'IN' ? 'positive' : 'negative' }}">
                                        {{ $Transaction->type === 'IN' ? '+' : '-' }}{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
                        <div class="table-responsive">
                            <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="dich-vu">
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
                                    @foreach($Dich_Vu as $Transaction)
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

<script>
$(document).ready(function() {
    // DataTable configuration
    const dataTableConfig = {
        "language": {
            "search": "Tìm kiếm:",
            "lengthMenu": "Hiển thị _MENU_ giao dịch",
            "info": "Hiển thị _START_ đến _END_ của _TOTAL_ giao dịch",
            "infoEmpty": "Hiển thị 0 đến 0 của 0 giao dịch",
            "infoFiltered": "(lọc từ _MAX_ giao dịch)",
            "paginate": {
                "first": "Đầu",
                "last": "Cuối",
                "next": "Tiếp",
                "previous": "Trước"
            },
            "emptyTable": "Không có dữ liệu",
            "zeroRecords": "Không tìm thấy kết quả"
        },
        "scrollY": "calc(100vh - 400px)", // Chiều cao scroll
        "scrollCollapse": true,
        "paging": true,
        "pageLength": 25, // Tăng số dòng hiển thị
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tất cả"]],
        "order": [], // Không sort, giữ nguyên thứ tự từ database
        "ordering": false, // Disable tất cả sorting
        "columnDefs": [
            { "orderable": false, "targets": "_all" }, // Disable sort cho tất cả cột
            { "width": "180px", "targets": 0 }, // ID column
            { "width": "auto", "targets": 1 },  // Description column
            { "width": "160px", "targets": 2 }, // Date column
            { "width": "180px", "targets": 3 }, // Amount column
            { "width": "120px", "targets": 4 }  // Type column
        ],
        "autoWidth": false,
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "responsive": true
    };

    // Initialize DataTables for all tables (check if already initialized)
    function initDataTable(tableId) {
        if (!$.fn.DataTable.isDataTable('#' + tableId)) {
            const tableConfig = Object.assign({}, dataTableConfig);
            // Force disable ordering cho từng table
            tableConfig.ordering = false;
            tableConfig.order = [];
            $('#' + tableId).DataTable(tableConfig);
            console.log('Initialized DataTable for:', tableId, 'with no ordering');
        } else {
            console.log('DataTable already initialized for:', tableId);
        }
    }

    // Initialize all tables
    initDataTable('all');
    initDataTable('bill-si');
    initDataTable('nap');
    initDataTable('ADS');
    initDataTable('dich-vu');
    
    // Initialize other tables that might exist
    $('table').not('#all, #bill-si, #nap, #ADS, #dich-vu').each(function() {
        const tableId = $(this).attr('id');
        if (tableId && !$.fn.DataTable.isDataTable('#' + tableId)) {
            $(this).DataTable(dataTableConfig);
            console.log('Initialized DataTable for:', tableId);
        }
    });

    // Mobile search functionality
    const mobileSearchInput = $('#mobileTransactionSearch');
    const mobileClearButton = $('#mobileClearSearch');
    
    // Function to sort mobile cards by date (descending)
    function sortMobileCardsByDate(targetId) {
        const container = $('#' + targetId + ' .mobile-cards-container');
        const cards = container.find('.transaction-card').toArray();
        
        // Sort cards by date (descending)
        cards.sort(function(a, b) {
            const dateA = $(a).find('.card-info-row').first().find('.card-info-value').text().trim();
            const dateB = $(b).find('.card-info-row').first().find('.card-info-value').text().trim();
            
            // Convert to Date objects for comparison
            const dateObjA = new Date(dateA);
            const dateObjB = new Date(dateB);
            
            // Sort descending (newest first)
            return dateObjB - dateObjA;
        });
        
        // Re-append sorted cards to container
        cards.forEach(function(card) {
            container.append(card);
        });
        
        console.log('Sorted mobile cards by date for:', targetId);
    }
    
    function performMobileSearch() {
        const searchTerm = mobileSearchInput.val().toLowerCase().trim();
        console.log('Mobile search for:', searchTerm);
        
        // Get current active tab
        const activeTab = $('.nav-pills .nav-link.active');
        if (activeTab.length === 0) return;
        
        const targetId = activeTab.attr('data-bs-target').replace('#', '');
        const cards = $('#' + targetId + ' .transaction-card');
        
        console.log('Active tab:', targetId);
        console.log('Cards found:', cards.length);
        
        let visibleCount = 0;
        
        cards.each(function() {
            const card = $(this);
            const transactionId = card.find('.card-transaction-id').text().toLowerCase();
            const description = card.find('.card-description').text().toLowerCase();
            const amount = card.find('.card-amount-value').text().toLowerCase();
            
            const matches = transactionId.includes(searchTerm) || 
                          description.includes(searchTerm) || 
                          amount.includes(searchTerm);
            
            if (matches) {
                card.removeClass('hidden');
                visibleCount++;
            } else {
                card.addClass('hidden');
            }
        });
        
        console.log('Visible cards:', visibleCount);
        
        // Sort visible cards by date after search
        if (searchTerm === '') {
            // Only sort when not searching (all cards visible)
            sortMobileCardsByDate(targetId);
        }
        
        // Show/hide clear button
        if (searchTerm !== '') {
            mobileClearButton.show();
        } else {
            mobileClearButton.hide();
        }
    }
    
    function clearMobileSearch() {
        mobileSearchInput.val('');
        mobileClearButton.hide();
        
        // Show all cards
        $('.transaction-card').removeClass('hidden');
        
        console.log('Mobile search cleared');
    }
    
    // Mobile search event listeners
    mobileSearchInput.on('input keyup', function() {
        performMobileSearch();
    });
    
    mobileClearButton.on('click', function() {
        clearMobileSearch();
    });
    
    // Reset mobile search when switching tabs
    $('.nav-pills .nav-link').on('click', function() {
        setTimeout(function() {
            performMobileSearch();
        }, 200);
    });
    
    // Sort mobile cards by date on page load and tab switch
    function initializeMobileSorting() {
        const tabs = ['pills-all', 'pills-bill-si', 'pills-nap', 'pills-ADS', 'pills-dich-vu'];
        tabs.forEach(function(tabId) {
            sortMobileCardsByDate(tabId);
        });
    }
    
    // Initialize mobile search and sorting
    mobileClearButton.hide();
    
    // Sort cards on page load
    setTimeout(function() {
        initializeMobileSorting();
    }, 500);
    
    // Sort cards when switching tabs
    $('.nav-pills .nav-link').on('shown.bs.tab', function() {
        const targetId = $(this).attr('data-bs-target').replace('#', '');
        setTimeout(function() {
            sortMobileCardsByDate(targetId);
        }, 100);
    });
});
</script>

@endsection