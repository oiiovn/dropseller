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

    .table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 2;
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

    .lazy-load {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .lazy-load.loaded {
        opacity: 1;
    }
</style>
<div class="container-fluid" style=" width: 100%; background: white; ">
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">
                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#all-orders" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả đơn hàng
                                </a>
                            </li>
                            @foreach($orders_all as $userName => $shops)
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered cursor-pointer" href="#user-{{ Str::slug($userName) }}" data-bs-toggle="tab" role="tab" aria-selected="false" style="cursor: pointer;">
                                    {{ $userName }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <!-- All orders tab -->
                            <div class="tab-pane fade show active" id="all-orders">
                                <div class="table-responsive table-card mb-1">
                                    <table id="order-server" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã đơn</th>
                                                <th>Shop</th>
                                                <th>Ngày tạo</th>
                                                <th>Ngày lọc</th>
                                                <th>Số lượng</th>
                                                <th>Phí Drop</th>
                                                <th>Tổng Bill</th>
                                                <th>Thanh toán</th>
                                                <th>Mã giao dịch</th>
                                                <th>Đối soát</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- User-specific tabs -->
                            @foreach($orders_all as $userName => $shops)
                            <div class="tab-pane fade" id="user-{{ Str::slug($userName) }}">
                                <div class="table-responsive table-card mb-1">
                                    <table id="user-table-{{ Str::slug($userName) }}" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã đơn</th>
                                                <th>Shop</th>
                                                <th>Ngày tạo</th>
                                                <th>Ngày lọc</th>
                                                <th>Số lượng</th>
                                                <th>Phí Drop</th>
                                                <th>Tổng Bill</th>
                                                <th>Thanh toán</th>
                                                <th>Mã giao dịch</th>
                                                <th>Đối soát</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal Template -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow: hidden;">
                <div class="row">
                    <!-- Product details section - scrollable -->
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Danh sách sản phẩm</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                                    <table class="table table-hover" id="orderDetailTable">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderDetailBody">
                                            <!-- Order details will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order summary section - fixed -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title" id="orderCodeDisplay"></h5>
                            </div>
                            <div class="card-body">
                                <div id="orderSummary">
                                    <!-- Order summary will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId;
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>

<script>
    // Function to load and display order details in modal
    function loadOrderDetails(orderId) {
        $.ajax({
            url: '/api/orders/' + orderId + '/details',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate order details
                    $('#orderDetailBody').empty();
                    $('#orderCodeDisplay').text('Mã đơn: ' + response.order.order_code);
                    
                    // Add order details to table
                    response.details.forEach(function(detail) {
                        $('#orderDetailBody').append(`
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="${detail.image || '/assets/images/no-image.jpg'}" alt="" class="avatar-sm rounded">
                                        </div>
                                        <div>
                                            <h6 class="font-size-14 mb-1">${detail.product_name}</h6>
                                            <p class="text-muted mb-0 font-size-12">SKU: ${detail.sku}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">${detail.quantity}</td>
                                <td class="text-end">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(detail.unit_cost)}</td>
                                <td class="text-end">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(detail.total_cost)}</td>
                            </tr>
                        `);
                    });
                    
                    // Populate summary
                    $('#orderSummary').html(`
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>Shop:</td>
                                        <td class="fw-medium">${response.shop?.shop_name || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td>Ngày đặt:</td>
                                        <td class="fw-medium">${response.order.filter_date}</td>
                                    </tr>
                                    <tr>
                                        <td>Tổng số sản phẩm:</td>
                                        <td class="fw-medium">${response.order.total_products}</td>
                                    </tr>
                                    <tr>
                                        <td>Phí dropship:</td>
                                        <td class="fw-medium">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(response.order.total_dropship)}</td>
                                    </tr>
                                    <tr>
                                        <td>Tổng tiền nhập hàng:</td>
                                        <td class="fw-medium">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(response.order.total_bill - response.order.total_dropship)}</td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>Tổng tiền đơn hàng:</strong></td>
                                        <td><strong>${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(response.order.total_bill)}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Trạng thái thanh toán:</td>
                                        <td class="${response.order.payment_status === 'Chưa thanh toán' ? 'text-danger' : 'text-success'} fw-medium">
                                            ${response.order.payment_status}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Trạng thái đối soát:</td>
                                        <td class="${response.order.reconciled ? 'text-danger' : 'text-success'} fw-medium">
                                            ${response.order.reconciled ? 'Chưa đối soát' : 'Đã đối soát'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);
                    
                    // Show the modal
                    const orderDetailModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                    orderDetailModal.show();
                } else {
                    alert('Không thể tải chi tiết đơn hàng: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải chi tiết đơn hàng');
            }
        });
    }

    $(document).ready(function() {
        // Initialize main DataTable
        const mainTable = $('#order-server').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("orders.data") }}',
            columns: [
                {
                    data: 'order_code',
                    name: 'order_code',
                    render: function(data) {
                        return '<span class="hienthicopy">' + data + 
                               '<span class="ri-clipboard-line icon ms-1" data-clipboard="' + data + '"></span></span>';
                    }
                },
                {
                    data: 'shop_name',
                    name: 'shop.shop_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'filter_date',
                    name: 'filter_date'
                },
                {
                    data: 'total_products',
                    name: 'total_products'
                },
                {
                    data: 'total_dropship',
                    name: 'total_dropship'
                },
                {
                    data: 'total_bill',
                    name: 'total_bill'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'transaction_id',
                    name: 'transaction_id'
                },
                {
                    data: 'reconciled',
                    name: 'reconciled'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-primary view-details" data-id="' + row.id + '">' +
                               '<i class="ri-eye-line"></i></button>';
                    }
                }
            ],
            language: {
                lengthMenu: "Hiển thị _MENU_ đơn",
                zeroRecords: "Không tìm thấy dữ liệu",
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ đơn",
                infoEmpty: "Không có dữ liệu",
                infoFiltered: "(lọc từ _MAX_ tổng đơn)",
                search: "Tìm:",
                paginate: {
                    first: "Đầu",
                    last: "Cuối",
                    next: "Tiếp",
                    previous: "Trước"
                }
            }
        });
        
        // Handle click on view details button
        $(document).on('click', '.view-details', function() {
            const orderId = $(this).data('id');
            loadOrderDetails(orderId);
        });
        
        // Initialize copy functionality
        $(document).on('click', '[data-clipboard]', function(e) {
            e.stopPropagation();
            const text = $(this).data('clipboard');
            navigator.clipboard.writeText(text).then(() => {
                $(this).removeClass('ri-clipboard-line').addClass('ri-check-line text-success');
                setTimeout(() => {
                    $(this).removeClass('ri-check-line text-success').addClass('ri-clipboard-line');
                }, 1500);
            });
        });
        
        // Initialize user-specific tables when tab is clicked
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr("href");
            if (target.startsWith('#user-')) {
                const userName = target.replace('#user-', '');
                const tableId = '#user-table-' + userName;
                
                if (!$.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route("orders.data") }}',
                            data: function(d) {
                                d.user = userName;
                            }
                        },
                        columns: [
                            {
                                data: 'order_code',
                                name: 'order_code',
                                render: function(data) {
                                    return '<span class="hienthicopy">' + data + 
                                        '<span class="ri-clipboard-line icon ms-1" data-clipboard="' + data + '"></span></span>';
                                }
                            },
                            {
                                data: 'shop_name',
                                name: 'shop.shop_name'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'filter_date',
                                name: 'filter_date'
                            },
                            {
                                data: 'total_products',
                                name: 'total_products'
                            },
                            {
                                data: 'total_dropship',
                                name: 'total_dropship'
                            },
                            {
                                data: 'total_bill',
                                name: 'total_bill'
                            },
                            {
                                data: 'payment_status',
                                name: 'payment_status'
                            },
                            {
                                data: 'transaction_id',
                                name: 'transaction_id'
                            },
                            {
                                data: 'reconciled',
                                name: 'reconciled'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return '<button class="btn btn-sm btn-primary view-details" data-id="' + row.id + '">' +
                                           '<i class="ri-eye-line"></i></button>';
                                }
                            }
                        ],
                        language: {
                            lengthMenu: "Hiển thị _MENU_ đơn",
                            zeroRecords: "Không tìm thấy dữ liệu",
                            info: "Hiển thị _START_ đến _END_ của _TOTAL_ đơn",
                            infoEmpty: "Không có dữ liệu",
                            infoFiltered: "(lọc từ _MAX_ tổng đơn)",
                            search: "Tìm:",
                            paginate: {
                                first: "Đầu",
                                last: "Cuối",
                                next: "Tiếp",
                                previous: "Trước"
                            }
                        }
                    });
                }
            }
        });
    });
</script>
@endsection