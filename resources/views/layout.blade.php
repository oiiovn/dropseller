<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:44:28 GMT -->

<head>

    <meta charset="utf-8" />
    <title>Dropship | Seller - Custommer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="https://img.icons8.com/windows/512/blog-logo.png">

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Thư viện ngôn ngữ tiếng Việt -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <!-- Include DataTables JS -->
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/libs/dropzone/dropzone.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-papmM0swSgqMCZ3K6mQUC9ErcRgx+JKTxBb8A5kPufHrX7IrCKl+FddnhgN8N6Wa+IV+aUe1dYtTDv9pLMtzNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS (nếu chưa có) -->

    <!-- Bootstrap Bundle JS (Bao gồm Popper) -->

    



    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        a {
            text-decoration: none !important;
        }

        body {
            font-size: 14px !important;
            font-family: Arial, sans-serif !important;

        }

        /* SCC thống báo ngắn */
        .toast {
            display: inline-block;
            padding: 10px 20px;
            background-color: rgb(80, 199, 199);
            color: black;
            border-radius: 5px;
            margin-top: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s, transform 0.3s;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading indicator style */
        #loading-indicator {
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.8);
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>

</head>

<body>
    <!-- Thông báo 3 giây -->
    <div id="toast-container" style="position: fixed; top: 90px; right: 20px; z-index: 1000;"></div>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('header')
        <!-- ========== App Menu ========== -->
        @include('navbar')
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div style="padding-top:70px;">
                @if (session('success'))
                <div class="alert alert-success" id="successMessage">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger" id="errorMessage">
                    {{ session('error') }}
                </div>
                @endif
                @include('noti.noti')
                <div id="main-content">


                    @yield('main')
                </div>

                <!-- End Page-content -->
            </div>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <!-- <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button> -->
    <!--end back-to-top-->

    <!--preloader-->
    <!-- <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div> -->

    <!-- <div class="customizer-setting d-none d-md-block">
        <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas"
            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
            <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
        </div>
    </div> -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>
    <script src="{{ asset('js/gridjs.init.js') }}"></script>
    <script src="assets/libs/gridjs/gridjs.umd.js"></script>
    <!-- gridjs init -->
    <script src="assets/js/pages/gridjs.init.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <script>
        // Gọi lại hàm thông báo ngắn
        function showToast(message) {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = message;

            toastContainer.appendChild(toast);

            // Show the toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Hide the toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 100);
            }, 2000);
        }
    </script>
    <script>
        // Tự động ẩn thông báo sau 3 giây (3000ms)
        setTimeout(function() {
            let successAlert = document.getElementById('successMessage');
            let errorAlert = document.getElementById('errorMessage');

            if (successAlert) {
                successAlert.style.transition = "opacity 0.5s";
                successAlert.style.opacity = 0;
                setTimeout(() => successAlert.remove(), 500);
            }

            if (errorAlert) {
                errorAlert.style.transition = "opacity 0.5s";
                errorAlert.style.opacity = 0;
                setTimeout(() => errorAlert.remove(), 500);
            }
        }, 3000);
    </script>
    <script>
        // Gắn sự kiện click vào nút
        document.getElementById('markReadButton').addEventListener('click', function() {
            // Gửi AJAX request để đánh dấu các thông báo là đã đọc
            fetch("{{ route('notifications.markRead') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Đảm bảo gửi CSRF token
                    },
                    body: JSON.stringify({
                        user_id: "{{ Auth::id() }}" // Thêm thông tin người dùng nếu cần
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Cập nhật lại số lượng thông báo chưa đọc trên giao diện
                    document.getElementById('nav-profile-tab').innerText = 'Thông báo mới (0)';
                })
                .catch(error => {
                    console.error('Có lỗi xảy ra:', error);
                });
        });
    </script>
    <script>
        function clearSearchInput() {
            document.querySelector('.search-box input').value = '';
            document.querySelector('.search-box input').dispatchEvent(new Event('input'));
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        $(document).ready(function() {
            const $mainContent = $('#main-content');
            const pageCache = new Map();
            let isLoading = false;

            // Hàm xử lý loading
            function showLoading() {
                if (!$('#loading-indicator').length) {
                    $('body').append(`
                        <div id="loading-indicator" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);
                }
            }

            function hideLoading() {
                $('#loading-indicator').remove();
            }

            // Sửa lại hàm loadPage
            async function loadPage(url, pushState = true) {
                if (isLoading) return;

                try {
                    isLoading = true;
                    showLoading();

                    // Kiểm tra cache
                    if (pageCache.has(url)) {
                        const cachedData = pageCache.get(url);
                        if (cachedData) {
                            $mainContent.html(cachedData);
                            if (pushState) {
                                window.history.pushState({
                                    url: url
                                }, '', url);
                            }
                            initFeatures();
                            hideLoading();
                            return;
                        }
                    }

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html, application/xhtml+xml',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const html = await response.text();

                    // Kiểm tra xem response có phải là JSON error không
                    try {
                        const jsonResponse = JSON.parse(html);
                        if (jsonResponse.error) {
                            throw new Error(jsonResponse.error);
                        }
                    } catch (e) {
                        // Không phải JSON, tiếp tục xử lý như HTML
                    }

                    const $temp = $('<div>').html(html);
                    const newContent = $temp.find('#main-content').html();

                    if (!newContent) {
                        throw new Error('Không tìm thấy nội dung trong response');
                    }

                    // Cập nhật nội dung
                    $mainContent.html(newContent);

                    // Lưu cache với thời gian sống 5 phút
                    pageCache.set(url, newContent);
                    setTimeout(() => pageCache.delete(url), 5 * 60 * 1000);

                    // Cập nhật URL nếu cần
                    if (pushState) {
                        window.history.pushState({
                            url: url
                        }, '', url);
                    }

                    // Khởi tạo lại các tính năng
                    initFeatures();

                } catch (error) {
                    console.error('Load page error:', error);
                    showToast(error.message || 'Có lỗi xảy ra, vui lòng thử lại sau');

                    // Nếu lỗi 401 (Unauthorized) hoặc 419 (CSRF token mismatch)
                    if (error.status === 401 || error.status === 419) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } finally {
                    isLoading = false;
                    hideLoading();
                }
            }

            // Sửa lại xử lý sự kiện click
            $(document).on('click', '.ajax-link', function(e) {
                e.preventDefault();
                const url = this.href;
                loadPage(url);
            });

            // Xử lý nút back/forward
            window.onpopstate = function(event) {
                if (event.state && event.state.url) {
                    loadPage(event.state.url, false);
                }
            };

            // Khởi tạo DataTable với các tùy chọn tối ưu
            function initDataTable($table) {
                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().clear().destroy(); // Ensure proper cleanup
                }

                return $table.DataTable({
                    serverSide: false,
                    processing: true,
                    pageLength: 10,
                    deferRender: true,
                    stateSave: true, // Save table state to avoid reloading
                    deferLoading: 0, // Prevent initial loading delay
                    lengthMenu: [10, 20, 50],
                    order: [
                        [2, "desc"]
                    ],
                    language: {
                        processing: "Đang xử lý...",
                        search: "🔍",
                        lengthMenu: "Hiển thị _MENU_ dòng",
                        info: "Hiển thị _START_ đến _END_ của _TOTAL_ dòng",
                        infoEmpty: "Không có dữ liệu",
                        infoFiltered: "(lọc từ _MAX_ dòng)",
                        paginate: {
                            first: "Đầu",
                            last: "Cuối",
                            next: "Sau",
                            previous: "Trước"
                        }
                    },
                    initComplete: function() {
                        console.log('DataTable initialized successfully'); // Debugging log
                    }
                });
            }

            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function initFeatures() {
                // Cleanup and reinitialize DataTables
                const debouncedInitDataTable = debounce(function($table) {
                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().clear().destroy(); // Ensure proper cleanup
                    }
                    initDataTable($table); // Smooth reinitialization
                }, 300); // Debounce delay to prevent rapid reinitializations

                $('.datatable').each(function() {
                    debouncedInitDataTable($(this));
                });

                // Khởi tạo copy functionality
                initOrderCopy();
                
                // Luôn gọi lại OrderPage nếu là trang order
                if (window.location.pathname.includes('/order') || 
                    window.location.pathname.includes('/don-hang') ||
                    $('#orderList').length > 0) {
                    console.log('🔄 Order page detected, initializing OrderPage...');
                    if (typeof window.initializeOrderPage === 'function') {
                        setTimeout(() => {
                            window.initializeOrderPage();
                        }, 100);
                    }
                }
                // Luôn gọi lại mobile filter nếu có mobile filter bar
                if (document.getElementById('mobile-filter-bar') && typeof window.initOrderMobile === 'function') {
                    setTimeout(() => {
                        window.initOrderMobile();
                    }, 100);
                }
                
                // Khởi tạo lại JavaScript cho trang balance history
                if (window.location.pathname.includes('/balance/history') || 
                    document.getElementById('balance-history-cards')) {
                    console.log('🔄 Balance history page detected, initializing balance history...');
                    initBalanceHistory();
                }
            }

            // Khởi tạo ban đầu
            initFeatures();
        });
    </script>
    <script>
        function initOrderCopy() {
            // Remove all existing copy event listeners first
            document.querySelectorAll('.order-link .icon').forEach(icon => {
                if (icon._copyHandler) {
                    icon.removeEventListener('click', icon._copyHandler);
                    delete icon._copyHandler;
                }
            });

            const orderLinks = document.querySelectorAll('.order-link');
            orderLinks.forEach(link => {
                const icon = link.querySelector('.icon');
                const orderCode = link.getAttribute('data-order-code');
                
                if (!icon || !orderCode) return;

                // Create a unique handler for this icon
                icon._copyHandler = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Check if already copying
                    if (icon.dataset.copying === 'true') return;
                    
                    icon.dataset.copying = 'true';
                    
                    navigator.clipboard.writeText(orderCode)
                        .then(() => {
                            showToast(`Đã copy mã: ${orderCode} !`);
                        })
                        .catch(err => {
                            console.error('Không có dữ liệu copy: ', err);
                            showToast('Lỗi khi copy!');
                        })
                        .finally(() => {
                            setTimeout(() => {
                                icon.dataset.copying = 'false';
                            }, 1000);
                        });
                };

                icon.addEventListener('click', icon._copyHandler);
            });
        }
        
        function initBalanceHistory() {
            const historyCards = document.getElementById('balance-history-cards');
            const paginationNav = document.getElementById('balance-pagination');
            const searchForm = document.getElementById('balance-search-form');
            const searchInput = document.getElementById('search-input');
            const typeSelect = document.getElementById('type-select');

            if (!historyCards || !searchForm) return;

            // Xóa event listeners cũ nếu có
            if (searchForm._submitHandler) {
                searchForm.removeEventListener('submit', searchForm._submitHandler);
            }
            if (paginationNav._clickHandler) {
                paginationNav.removeEventListener('click', paginationNav._clickHandler);
            }

            // Lấy danh sách loại giao dịch cho dropdown
            fetch('/api/balance-history/types')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Xóa options cũ trừ option đầu tiên
                        while (typeSelect.children.length > 1) {
                            typeSelect.removeChild(typeSelect.lastChild);
                        }
                        data.data.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt.value;
                            option.textContent = opt.label;
                            typeSelect.appendChild(option);
                        });
                    }
                });

            // Hàm render lịch sử số dư
            function renderHistories(histories) {
                historyCards.innerHTML = '';
                if (!histories.length) {
                    historyCards.innerHTML = '<div class="col-12"><div class="alert alert-info">Không có dữ liệu để hiển thị.</div></div>';
                    return;
                }
                histories.forEach(item => {
                    historyCards.innerHTML += `
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card balance-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="balance-icon">
                                            ${getIcon(item.type)}
                                        </span>
                                        <span class="badge balance-type-badge">${item.type_label}</span>
                                    </div>
                                    <span class="text-muted small">${item.created_at}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted">Mã GD:</span> <span class="fw-semibold text-primary">${item.transaction_code}</span>
                                </div>
                                <div class="mb-2 fw-bold balance-amount ${item.is_positive ? 'text-success' : 'text-danger'}">
                                    ${item.formatted_amount}
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted">Số dư sau:</span> <span class="fw-semibold">${item.formatted_balance}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted">Ghi chú:</span>
                                    ${item.note ? `<span class="note-box">${item.note}</span>` : '<span class="text-muted">-</span>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });
            }

            // Hàm render phân trang
            function renderPagination(pagination) {
                if (pagination.last_page <= 1) {
                    paginationNav.innerHTML = '';
                    return;
                }
                let html = '<ul class="pagination">';
                // Nút Previous
                if (pagination.current_page > 1) {
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}"><i class="bi bi-chevron-left"></i></a></li>`;
                }
                // Tính toán range trang
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
                if (startPage > 1) {
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (startPage > 2) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }
                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item${i === pagination.current_page ? ' active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
                }
                // Nút Next
                if (pagination.current_page < pagination.last_page) {
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}"><i class="bi bi-chevron-right"></i></a></li>`;
                }
                html += '</ul>';
                paginationNav.innerHTML = html;
            }

            // Lấy icon theo loại giao dịch
            function getIcon(type) {
                switch(type) {
                    case 'deposit': return '<i class="bi bi-arrow-down-circle-fill text-success"></i>';
                    case 'withdraw': return '<i class="bi bi-arrow-up-circle-fill text-danger"></i>';
                    case 'order': return '<i class="bi bi-bag-check-fill text-warning"></i>';
                    case 'refund': return '<i class="bi bi-arrow-repeat text-info"></i>';
                    case 'ads': return '<i class="bi bi-bullseye text-dark"></i>';
                    case 'Monthly': return '<i class="bi bi-calendar-check text-dark"></i>';
                    case 'product_fee': return '<i class="bi bi-cash-coin text-secondary"></i>';
                    default: return '<i class="bi bi-cash-stack text-secondary"></i>';
                }
            }

            // Hàm load dữ liệu từ API
            function loadHistories(page = 1) {
                const params = new URLSearchParams();
                if (searchInput.value) params.append('search', searchInput.value);
                if (typeSelect.value) params.append('type', typeSelect.value);
                params.append('page', page);
                params.append('limit', 12);

                const url = '/api/balance-history?' + params.toString();
                console.log('Loading data from:', url);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        console.log('API Response:', data);
                        if (data.success) {
                            renderHistories(data.data.histories);
                            renderPagination(data.data.pagination);
                        } else {
                            console.error('API Error:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                    });
            }

            // Sự kiện submit form tìm kiếm/lọc
            searchForm._submitHandler = function(e) {
                e.preventDefault();
                loadHistories(1);
            };
            searchForm.addEventListener('submit', searchForm._submitHandler);

            // Sự kiện click phân trang
            paginationNav._clickHandler = function(e) {
                if (e.target.classList.contains('page-link')) {
                    e.preventDefault();
                    const page = parseInt(e.target.getAttribute('data-page'));
                    loadHistories(page);
                }
            };
            paginationNav.addEventListener('click', paginationNav._clickHandler);

            // Load lần đầu
            loadHistories();
        }
    </script>
    <script>
        setInterval(function() {
            fetch('{{ route("keep-alive") }}', {
                credentials: 'same-origin'
            });
        }, 5 * 60 * 1000); // 5 phút ping 1 lần
    </script>

    </div> {{-- Đóng container chính --}}
    
    {{-- Thêm script kiểm tra session --}}
    <script>
        function checkSession() {
            fetch('/keep-alive', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    window.location.href = "{{ route('login') }}";
                }
            })
            .catch(() => {
                window.location.href = "{{ route('login') }}";
            });
        }

        // Kiểm tra mỗi 5 phút
        setInterval(checkSession, 5 * 60 * 1000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>