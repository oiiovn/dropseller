@extends('layout')
@section('title', 'main')

@section('main')

<style>
    .hienthicopy .icon {
        display: none;
        cursor: pointer;
    }

    .hienthicopy:hover .icon {
        display: inline;
    }

    .search-box .clear-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        display: none;
    }

    .search-box input:valid~.clear-icon {
        display: inline;
    }

    .tooltip-inner {
        background-color: #ffffff !important;
        color: #000 !important;
        padding: 10px 12px !important;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: left;
        max-width: 260px;
        font-size: 13px;
        opacity: 1 !important;
    }

    .tooltip.show {
        opacity: 1 !important;
    }

    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #ffffff !important;
    }

    .user-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0;
        margin: 0 2px;
    }

    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-code {
        font-size: 0.9rem;
        color: #6c757d;
        
    }

    .user-stats {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .stat-item {
        text-align: center;
        flex: 1;
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2e397f;
        display: block;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .shop-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #f8f9fa;
        border-radius: 6px;
        font-size: 0.8rem;
        margin: 0.25rem 0;
    }

    .shop-icon {
        width: 16px;
        height: 16px;
    }

    .payment-warning {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 0.75rem;
        text-align: center;
    }

    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: none;
        background: #007bff;
        color: white;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        background: #0056b3;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .search-container {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 0.75rem;
    }

    .search-input {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .filters-container {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .filter-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .filter-btn:hover {
        background: #f8f9fa;
    }

    .filter-btn.active:hover {
        background: #0056b3;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .page-link {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border: 1px solid #dee2e6;
        background: white;
        color: #000;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .page-link.active {
        background: #007bff;
        color: white;
       
    }
    .page-link.disabled{
        border: 1px solid #dee2e6;
        background: white;;
    }

    .no-results {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .no-results i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .user-stats {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filters-container {
            flex-direction: column;
        }
        
        .search-container {
            padding: 1rem;
        }
    }
</style>

<div class="container-fluid mt-2" style="width: 100%; background: #f8f9fa; min-height: 100vh;">
    <div class="row">
        <div class="col-12">
            <!-- Search and Filters -->
            <div class="search-container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <input type="text" id="searchInput" class="form-control search-input" placeholder="Tìm kiếm khách hàng...">
                        </div>
                    </div>
                    <div class="col-md-6">
                                                 <div class="filters-container">
                             <button class="filter-btn active" data-filter="all">Tất cả</button>
                             <button class="filter-btn" data-filter="admin">Admin</button>
                             <button class="filter-btn" data-filter="official">Nhà bán chính thức</button>
                             <button class="filter-btn" data-filter="dropship">Nhà bán dropship</button>
                             <button class="filter-btn" data-filter="overdue">Đơn quá hạn</button>
                         </div>
                    </div>
                </div>
            </div>

                         <!-- User Cards Container -->
             <div class="row g-2" id="userCardsContainer">
                @foreach($users as $user)
                    <div class="col-lg-2 col-md-6 col-sm-12 mb-1 user-card-wrapper" 
                      data-user-type="{{ $user->name == 'CEO' ? 'admin' : (in_array($user->name, ['Bùi Quốc Vũ', 'Vân', 'Trần Hoàng']) ? 'official' : 'dropship') }}"
                      data-has-overdue="{{ $user->shops->where('orders_unpaid_count', '>', 0)->count() > 0 ? 'true' : 'false' }}">
                    <div class="card user-card h-100">
                        <div class="card-body p-3">
                                                         <!-- User Header -->
                             <div class="d-flex align-items-start mb-2">
                                <div class="flex-shrink-0 me-3">
                                    <img src="@if(isset($user->image) && !empty($user->image)){{ $user->image }}@else https://img.icons8.com/ios-filled/100/user-male-circle.png @endif" 
                                         alt="{{ $user->name }}" class="user-avatar">
                                </div>
                                <div class="user-info">
                                                                         <h5 class="user-name">
                                         {{ $user->name }}
                                         @if($user->name == 'CEO')
                                             <i class="ri-shield-star-fill text-secondary" data-bs-toggle="tooltip" title="Admin"></i>
                                         @elseif(in_array($user->name, ['Bùi Quốc Vũ', 'Vân', 'Trần Hoàng']))
                                             <i class="ri-verified-badge-fill text-secondary" data-bs-toggle="tooltip" title="Nhà bán chính thức"></i>
                                         @else
                                             <i class="ri-verified-badge-fill text-muted" data-bs-toggle="tooltip" title="Nhà bán dropship"></i>
                                         @endif
                                     </h5>
                                    <div class="user-code">
                                        Code: <strong style="color:#2e397f;">{{ $user->referral_code ?? 'CODE' }}</strong>
                                    </div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>

                            <!-- User Stats -->
                            <div class="user-stats">
                                <div class="stat-item">
                                    <span class="stat-value">{{ number_format($user->total_amount, 0, ',', '.') }}</span>
                                    <span class="stat-label">VNĐ</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $user->shops->sum('orders_unpaid_count') }}</span>
                                    <span class="stat-label">Quá hạn</span>
                                </div>
                            </div>

                                                         <!-- Shops Info -->
                             <div class="mt-2">
                                @foreach ($user->shops as $shop)
                                    <div class="shop-badge">
                                        @if($shop->platform == 'Tiktok')
                                            <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" class="shop-icon">
                                        @elseif($shop->platform == 'Shoppe')
                                            <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" class="shop-icon">
                                        @else
                                            <i class="fas fa-store shop-icon"></i>
                                        @endif
                                        <span>{{ $shop->shop_name }}</span>
                                        @if($shop->orders_unpaid_count > 0)
                                            <span class="badge bg-danger ms-1">{{ $shop->orders_unpaid_count }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                                                         <!-- Action Button -->
                             <div class="mt-2 text-center">
                                <a href="#" class="action-btn" data-bs-toggle="modal" data-bs-target="#user-{{$user->id}}">
                                    <i class="ri-eye-fill"></i>
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for User Details -->
                <div class="modal fade" id="user-{{$user->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTitle-{{$user->id}}" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="modalTitle-{{$user->id}}">Đơn hàng chậm thanh toán - {{ $user->name }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã đơn nhập hàng</th>
                                                <th>Tên Chủ shop</th>
                                                <th>Shop</th>
                                                <th>Ngày tạo đơn</th>
                                                <th>Số lượng</th>
                                                <th>Phí drop</th>
                                                <th>Tổng Bill</th>
                                                <th>Thanh toán</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @php
                                                $hasOverdueOrders = false;
                                            @endphp
                                            @foreach ($user->shops as $shop)
                                                @if($shop->orders_unpaid_count > 0)
                                                    @foreach($shop->orders_unpaid as $orders_unpai)
                                                        @if($orders_unpai->payment_status == 'Chưa thanh toán')
                                                            @php $hasOverdueOrders = true; @endphp
                                                            <tr>
                                                                <td class="id text-black-50">
                                                                    <div class="hienthicopy">
                                                                        <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$orders_unpai->order_code}}">
                                                                            {{$orders_unpai['order_code']}}
                                                                            <span class="ri-checkbox-multiple-blank-line icon"></span>
                                                                        </a>
                                                                    </div>
                                                                    <div class="text-body-secondary" style="font-size: 11px;">{{$orders_unpai->filter_date}}</div>
                                                                </td>
                                                                <td class="customer_cost">
                                                                    {{ $orders_unpai->shop->user->name?? 'N/A' }}
                                                                </td>
                                                                <td class="customer_cost" data-shop-id="{{ optional($orders_unpai->shop)->id ?? 0 }}">
                                                                    @if($shop->platform == 'Tiktok')
                                                                        <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 20px; height: 20px;">
                                                                    @elseif($shop->platform == 'Shoppe')
                                                                        <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 20px; height: 20px;">
                                                                    @else
                                                                        <i class="fas fa-store me-1"></i>
                                                                    @endif
                                                                    {{ optional($orders_unpai->shop)->shop_name ?? 'N/A' }}
                                                                </td>
                                                                <td class="export_date">{{$orders_unpai->created_at}}</td>
                                                                <td class="total_products">{{$orders_unpai->total_products}}</td>
                                                                <td class="total_dropship">{{ number_format($orders_unpai->total_dropship, 0, ',', '.') }} đ</td>
                                                                <td class="total_bill">{{ number_format($orders_unpai->total_bill, 0, ',', '.') }} đ</td>
                                                                <td class="payment_status" style="color: red;">
                                                                    {{ $orders_unpai->payment_status }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            @if(!$hasOverdueOrders)
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="ri-check-line fs-1 text-success"></i>
                                                        <p class="mt-2 mb-0">Không có đơn hàng quá hạn thanh toán</p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="no-results" style="display: none;">
                <i class="ri-search-line"></i>
                <h5>Không tìm thấy khách hàng nào</h5>
                <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" id="paginationContainer">
                <!-- Pagination will be generated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentPage = 1;
    const itemsPerPage = 12;
    let filteredUsers = [];
    let allUsers = [];

    // Initialize all users
    function initializeUsers() {
        allUsers = $('.user-card-wrapper').toArray();
        filteredUsers = [...allUsers];
        updateDisplay();
    }

    // Filter users
    function filterUsers() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const activeFilter = $('.filter-btn.active').data('filter');
        
        filteredUsers = allUsers.filter(userCard => {
            const $card = $(userCard);
            const userName = $card.find('.user-name').text().toLowerCase();
            const userEmail = $card.find('.text-muted').text().toLowerCase();
            const userCode = $card.find('.user-code').text().toLowerCase();
            
            // Search filter
            const matchesSearch = userName.includes(searchTerm) || 
                                userEmail.includes(searchTerm) || 
                                userCode.includes(searchTerm);
            
                         // Type filter
             let matchesType = true;
             if (activeFilter === 'admin') {
                 matchesType = $card.data('user-type') === 'admin';
             } else if (activeFilter === 'official') {
                 matchesType = $card.data('user-type') === 'official';
             } else if (activeFilter === 'dropship') {
                 matchesType = $card.data('user-type') === 'dropship';
             } else if (activeFilter === 'overdue') {
                 matchesType = $card.attr('data-has-overdue') === 'true';
             }
            
            return matchesSearch && matchesType;
        });
        
        currentPage = 1;
        updateDisplay();
    }

    // Update display
    function updateDisplay() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const usersToShow = filteredUsers.slice(startIndex, endIndex);
        
        // Hide all cards
        $('.user-card-wrapper').hide();
        
        // Show filtered cards
        usersToShow.forEach(card => {
            $(card).show();
        });
        
        // Show/hide no results message
        if (filteredUsers.length === 0) {
            $('#noResults').show();
            $('#paginationContainer').hide();
        } else {
            $('#noResults').hide();
            $('#paginationContainer').show();
            generatePagination();
        }
    }

    // Generate pagination
    function generatePagination() {
        const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
        let paginationHtml = '';
        
        if (totalPages > 1) {
            // Previous button
            paginationHtml += `<a href="#" class="page-link ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}">Trước</a>`;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    paginationHtml += `<a href="#" class="page-link ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    paginationHtml += `<span class="page-link disabled">...</span>`;
                }
            }
            
            // Next button
            paginationHtml += `<a href="#" class="page-link ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}">Sau</a>`;
        }
        
        $('#paginationContainer').html(paginationHtml);
    }

    // Event listeners
    $('#searchInput').on('input', filterUsers);
    
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        filterUsers();
    });
    
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).hasClass('disabled')) {
            currentPage = page;
            updateDisplay();
        }
    });

    // Initialize tooltips
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize everything
    initializeUsers();
    initTooltips();

    // Reinitialize tooltips when modal opens
    $('.modal').on('shown.bs.modal', function() {
        initTooltips();
    });

    // Color coding for shop IDs in modal
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('.customer_cost').each(function() {
            const shopId = $(this).data('shop-id');
            if (shopId) {
                const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
                $(this).css('color', color);
            }
        });
    });
});
</script>

@endsection