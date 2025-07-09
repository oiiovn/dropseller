@extends('layout')
@section('title', 'Tất cả giao dịch')

@section('main')
<style>
    :root {
        --primary-color: #667eea;
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --light-bg: #f8fafc;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --border-radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .main-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 1rem 0;
    }

    .content-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin: 0 1rem;
    }

    .page-header {
        background: linear-gradient(135deg, #459fff 0%, #a93232 100%);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="1000,100 1000,0 0,100"/></svg>');
        background-size: cover;
    }

    .page-header .content {
        position: relative;
        z-index: 2;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-subtitle {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.25);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .nav-tabs-modern {
        border: none;
        background: var(--light-bg);
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        padding: 0px 0 0 15px;
        margin: 0;
        height: 50px;
        overflow: hidden;
        transition: height 0.3s ease;
        position: relative;
    }

    .nav-tabs-modern.expanded {
        height: auto;
        padding-bottom: 10px;
    }

    .nav-tabs-modern .nav-item {
        display: inline-flex;
        margin: 0;
        padding: 0;
    }

    .nav-tabs-modern .nav-link {
        border: none;
        border-radius: 8px;
        padding: 0 15px;
        background: transparent;
        color: #64748b;
        font-weight: 500;
        transition: var(--transition);
        height: 50px;
        display: flex;
        align-items: center;
        white-space: nowrap;
        font-size: 0.9rem;
        margin-right: 5px;
    }

    .nav-tabs-modern .nav-link.active {
        background: linear-gradient(135deg, #459fff 0%, #a93232 100%);
        color: white;
    }

    .nav-tabs-modern .nav-link:hover:not(.active) {
        background: rgba(102, 126, 234, 0.1);
        color: var(--primary-color);
    }

    .nav-tabs-modern .nav-link i {
        margin-right: 6px;
        font-size: 1rem;
    }

    .nav-tabs-modern .toggle-view {
        position: absolute;
        right: 0;
        top: 0;
        height: 50px;
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 0 10px;
        color: var(--primary-color);
        font-weight: 500;
        user-select: none;
        z-index: 1000;
      
    }

    .nav-tabs-modern .toggle-view:hover {
        opacity: 0.8;
    }

    .nav-tabs-modern .toggle-view i {
        margin-left: 6px;
        transition: transform 0.3s ease;
    }

    .nav-tabs-modern.expanded .toggle-view i {
        transform: rotate(180deg);
    }

    /* Ẩn các tab tràn khi chưa expanded */
    .nav-tabs-modern:not(.expanded) .nav-item:nth-child(n+14) {
        display: none;
    }

    /* Container cho các tab để tránh đè với nút toggle */
    .nav-tabs-modern .tabs-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding-right: 100px; /* Để chừa chỗ cho nút toggle */
        width: 100%;
    }

    .tab-content-modern {
        padding: 1rem;
        background: white;
    }

    .table-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .table-modern {
        margin: 0;
        font-size: 0.9rem;
    }

    .table-modern thead th {
        background: var(--light-bg);
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        color: #374151;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-modern thead th:nth-child(1) { width: 20%; }  /* Người chuyển */
    .table-modern thead th:nth-child(2) { width: 15%; }  /* Mã giao dịch */
    .table-modern thead th:nth-child(3) { width: 35%; }  /* Nội dung */
    .table-modern thead th:nth-child(4) { width: 15%; }  /* Số tiền */
    .table-modern thead th:nth-child(5) { width: 15%; }  /* Thời gian */

    .table-modern tbody td {
        padding: 1rem 0.75rem;
        border-top: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: var(--transition);
    }

    .table-modern tbody tr:hover {
        background: #f8fafc;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: var(--shadow-sm);
    }

    .user-info h5 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1f2937;
    }

    .transaction-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
    }

    .badge-income {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .badge-expense {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .badge-neutral {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .transaction-id {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        color: #6b7280;
        background: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .transaction-description {
        color: #374151;
        font-size: 0.9rem;
        line-height: 1.4;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: help;
    }

    .transaction-date {
        color: #6b7280;
        font-size: 0.85rem;
    }

    /* DataTables customization */
    .dataTables_wrapper {
        font-size: 0.9rem;
    }

    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        margin: 1rem 0;
    }

    .dataTables_length select,
    .dataTables_filter input {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.5rem;
        font-size: 0.9rem;
    }

    .dataTables_filter input {
        padding-left: 2.5rem;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>');
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        background-size: 1rem;
    }

    .dataTables_filter input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .dataTables_paginate .paginate_button {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        background: white;
        color: #374151;
        text-decoration: none;
        transition: var(--transition);
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .main-container {
            padding: 1rem 0;
        }
        
        .content-card {
            margin: 0 1rem;
        }
        
        .page-header {
            padding: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .tab-content-modern {
            padding: 1rem;
        }
        
        .table-responsive {
            border-radius: var(--border-radius);
        }

        .transaction-description {
            max-width: 150px;
            font-size: 0.8rem;
        }

        .table-modern thead th:nth-child(3) { width: 40%; }
        .table-modern thead th:nth-child(1) { width: 25%; }
        .table-modern thead th:nth-child(2) { width: 10%; }
        .table-modern thead th:nth-child(4) { width: 15%; }
        .table-modern thead th:nth-child(5) { width: 10%; }
    }

    /* Loading animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="main-container">
    <div class="content-card fade-in-up">
        <!-- Page Header -->
        <div class="page-header">
            <div class="content">
                <h1 class="page-title">
                    <i class="ri-exchange-dollar-line"></i>
                    Quản lý giao dịch
                </h1>
                <p class="page-subtitle">Theo dõi và quản lý tất cả các giao dịch trong hệ thống</p>
                
                <!-- Statistics -->
                <div class="stats-grid">
                    @php
                        $totalTransactions = 0;
                        $totalIncome = 0;
                        $totalExpense = 0;
                        
                        foreach($transactionsByReferral as $data) {
                            foreach($data['transactions'] as $transaction) {
                                $totalTransactions++;
                                if($transaction->type === 'IN') {
                                    $totalIncome += $transaction->amount;
                                } elseif($transaction->type === 'OUT') {
                                    $totalExpense += $transaction->amount;
                                }
                            }
                        }
                    @endphp
                    
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($totalTransactions) }}</div>
                        <div class="stat-label">Tổng giao dịch</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($totalIncome) }} VNĐ</div>
                        <div class="stat-label">Tổng thu</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($totalExpense) }} VNĐ</div>
                        <div class="stat-label">Tổng chi</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">{{ count($transactionsByReferral) }}</div>
                        <div class="stat-label">Người dùng</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs nav-tabs-modern" role="tablist">
            <div class="toggle-view">
                <i class="ri-arrow-down-s-line"></i>
            </div>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-transactions" role="tab" aria-selected="true">
                    <i class="ri-stack-line me-2"></i>Tất cả giao dịch
                </a>
            </li>
            @foreach($transactionsByReferral as $userId => $data)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#user-{{ $userId }}-content" role="tab">
                    <i class="ri-user-line me-2"></i>{{ $data['user']->name }}
                </a>
            </li>
            @endforeach
        </ul>

        <!-- Tab Content -->
        <div class="tab-content tab-content-modern">
            <!-- All Transactions Tab -->
            <div class="tab-pane fade show active" id="all-transactions" role="tabpanel">
                <div class="table-container">
                    <div class="table-responsive">
                        <table id="transaction_all" class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Người chuyển</th>
                                    <th>Mã giao dịch</th>
                                    <th>Nội dung</th>
                                    <th>Số tiền</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactionsByReferral as $data)
                                @foreach($data['transactions'] as $transaction)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $data['user']->image ?? 'https://img.icons8.com/ios-filled/100/user-male-circle.png' }}" 
                                                 alt="Avatar" class="user-avatar me-3">
                                            <div class="user-info">
                                                <h5>{{ $data['user']->name }}</h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="transaction-id">{{ $transaction->transaction_id }}</span>
                                    </td>
                                    <td>
                                        <div class="transaction-description" title="{{ $transaction->description }}">{{ $transaction->description }}</div>
                                    </td>
                                    <td>
                                        @if ($transaction->type === 'IN')
                                            <span class="transaction-badge badge-income">
                                                +{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @elseif ($transaction->type === 'OUT')
                                            <span class="transaction-badge badge-expense">
                                                -{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @else
                                            <span class="transaction-badge badge-neutral">
                                                {{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="transaction-date">{{ $transaction->created_at }}</div>
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>

            <!-- User-specific Transaction Tabs -->
            @foreach($transactionsByReferral as $userId => $data)
            <div class="tab-pane fade" id="user-{{ $userId }}-content" role="tabpanel">
                <div class="table-container">
                    <div class="table-responsive">
                        <table id="transaction-user-{{ $userId }}" class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Người chuyển</th>
                                    <th>Mã giao dịch</th>
                                    <th>Nội dung</th>
                                    <th>Số tiền</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['transactions'] as $transaction)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $data['user']->image ?? 'https://img.icons8.com/ios-filled/100/user-male-circle.png' }}" 
                                                 alt="Avatar" class="user-avatar me-3">
                                            <div class="user-info">
                                                <h5>{{ $data['user']->name }}</h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="transaction-id">{{ $transaction->transaction_id }}</span>
                                    </td>
                                    <td>
                                        <div class="transaction-description" title="{{ $transaction->description }}">{{ $transaction->description }}</div>
                                    </td>
                                    <td>
                                        @if ($transaction->type === 'IN')
                                            <span class="transaction-badge badge-income">
                                                +{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @elseif ($transaction->type === 'OUT')
                                            <span class="transaction-badge badge-expense">
                                                -{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @else
                                            <span class="transaction-badge badge-neutral">
                                                {{ number_format($transaction->amount, 0, '.', ',') }} VNĐ
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="transaction-date">{{ $transaction->created_at }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // DataTable configuration
    const dataTableConfig = {
        "paging": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "lengthMenu": [10, 25, 50, 100, 150],
        "pageLength": 25,
        "order": [[4, "desc"]],
        "responsive": true,
        "language": {
            "lengthMenu": "",
            "zeroRecords": "Không tìm thấy dữ liệu",
            "info": "",
            "infoEmpty": "Không có dữ liệu để hiển thị",
            "infoFiltered": "(lọc từ tổng số _MAX_ giao dịch)",
            "search": "",
            "paginate": {
                "first": "Đầu",
                "last": "Cuối",
                "next": "Tiếp",
                "previous": "Trước"
            },
            "loadingRecords": "Đang tải...",
            "processing": "Đang xử lý..."
        },
        "dom": '<"row"<"col-sm-12"tr>><"row"<"col-sm-5"l><"col-sm-7"p>>'
    };

    // Initialize main transactions table
    $('#transaction_all').DataTable(dataTableConfig);

    // Initialize user-specific tables
    @foreach($transactionsByReferral as $userId => $data)
    $('#transaction-user-{{ $userId }}').DataTable(dataTableConfig);
    @endforeach

    // Add smooth transitions when switching tabs
    $('a[data-bs-toggle="tab"]').on('click', function (e) {
        const tabsContainer = document.querySelector('.nav-tabs-modern');
        tabsContainer.classList.remove('expanded');
        const toggleBtn = tabsContainer.querySelector('.toggle-view i');
        if (toggleBtn) {
            toggleBtn.className = 'ri-arrow-down-s-line';
        }
    });

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
        // Scroll đến tab active sau khi chuyển
        const activeTab = e.target;
        if (activeTab) {
            setTimeout(() => {
                activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    });

    // Add loading animation to tables
    $('.table-container').each(function() {
        $(this).addClass('fade-in-up');
    });

    // Toggle button functionality
    const toggleBtn = document.querySelector('.toggle-view');
    const tabsContainer = document.querySelector('.nav-tabs-modern');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            tabsContainer.classList.toggle('expanded');
            const icon = toggleBtn.querySelector('i');
            
            if (tabsContainer.classList.contains('expanded')) {
                icon.className = 'ri-arrow-up-s-line';
            } else {
                icon.className = 'ri-arrow-down-s-line';
                // Scroll to active tab if it's hidden
                const activeTab = tabsContainer.querySelector('.nav-link.active');
                if (activeTab) {
                    activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        });
    }
});

// Custom color generation for transaction IDs
document.querySelectorAll('.transaction-id').forEach(el => {
    const transactionId = el.textContent.trim();
    if (transactionId) {
        const hash = transactionId.split('').reduce((a, b) => {
            a = ((a << 5) - a) + b.charCodeAt(0);
            return a & a;
        }, 0);
        const color = `#${((hash * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
        el.style.borderLeft = `3px solid ${color}`;
    }
});
</script>

@endsection