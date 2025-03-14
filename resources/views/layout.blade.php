<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:44:28 GMT -->

<head>

    <meta charset="utf-8" />
    <title>Drop_Ship_seller</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/h.png">

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
            <div class="page-content " style="padding-top:80px;">
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.ajax-link').on('click', function(e) {
            e.preventDefault(); // Ngăn trang reload
            
            let url = $(this).attr('href'); // Lấy URL từ thẻ <a>
            
            $.get(url, function(data) {
                $('#main-content').html($(data).find('#main-content').html()); // Cập nhật nội dung mới
                window.history.pushState(null, "", url); // Cập nhật URL mà không reload trang
            });
        });

        // Xử lý khi nhấn nút Back trên trình duyệt
        window.onpopstate = function(event) {
            location.reload();
        };
    });
</script>

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
        document.addEventListener('DOMContentLoaded', function() {
            const orderLinks = document.querySelectorAll('.order-link');
            orderLinks.forEach(link => {
                const icon = link.querySelector('.icon');
                const orderCode = link.getAttribute('data-order-code');
                let isThrottled = false;
                icon.addEventListener('click', function() {
                    if (isThrottled) return;
                    isThrottled = true;
                    // Copy the order code to clipboard
                    navigator.clipboard.writeText(orderCode)
                        .then(() => {
                            // Show notification
                            showToast(`Đã copy mã :  ${orderCode} !`);
                        })
                        .catch(err => {
                            console.error('Không có dữ liệu copy: ', err);
                        })
                        .finally(() => {
                            setTimeout(() => {
                                isThrottled = false;
                            }, 2200);
                        });
                });
            });

            $(document).ready(function() {
                $('#orderTable').DataTable({
                    "paging": true, // Bật phân trang
                    "searching": true, // Bật tìm kiếm
                    "ordering": true, // Bật sắp xếp
                    "info": true, // Hiển thị thông tin
                    "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                    "order": [
                        [2, "desc"]
                    ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                    // Chỉnh Tiếng Việt
                    "language": {
                        "lengthMenu": "Hiển thị _MENU_đơn hàng",
                        "zeroRecords": "Không tìm thấy dữ liệu",
                        "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                        "infoEmpty": "Không có dữ liệu để hiển thị",
                        "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                        "search": "🔍",
                        "paginate": {
                            "first": "Trang đầu",
                            "last": "Trang cuối",
                            "next": "Tiếp theo",
                            "previous": "Quay lại"
                        }
                    }
                });

            });
            $(document).ready(function() {
                $('#user_list').DataTable({
                    "paging": true, // Bật phân trang
                    "searching": true, // Bật tìm kiếm
                    "ordering": true, // Bật sắp xếp
                    "info": true, // Hiển thị thông tin
                    "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                    "order": [
                        [2, "desc"]
                    ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                    // Chỉnh Tiếng Việt
                    "language": {
                        "lengthMenu": "Hiển thị _MENU_đơn hàng",
                        "zeroRecords": "Không tìm thấy dữ liệu",
                        "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                        "infoEmpty": "Không có dữ liệu để hiển thị",
                        "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                        "search": "🔍",
                        "paginate": {
                            "first": "Trang đầu",
                            "last": "Trang cuối",
                            "next": "Tiếp theo",
                            "previous": "Quay lại"
                        }
                    }
                });

            });

        });

        function clearSearchInput() {
            document.querySelector('.search-box input').value = '';
            document.querySelector('.search-box input').dispatchEvent(new Event('input'));
        }
    </script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:45:33 GMT -->

</html>