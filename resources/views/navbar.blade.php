<div class="app-menu navbar-menu ">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Light Logo -->
        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-login.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-login.png') }}" alt="" height="58">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="assets/images/users/avatar-1.jpg" alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text">
                        <i class="ri ri-circle-fill fs-10 text-success align-baseline"></i>
                        <span class="align-middle">Online</span>
                    </span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="pages-profile.html">
                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Profile</span>
            </a>
            <a class="dropdown-item" href="apps-chat.html">
                <i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Messages</span>
            </a>
            <a class="dropdown-item" href="apps-tasks-kanban.html">
                <i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Taskboard</span>
            </a>
            <a class="dropdown-item" href="pages-faqs.html">
                <i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Help</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages-profile.html">
                <i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Balance: <b>$5971.67</b></span>
            </a>
            <a class="dropdown-item" href="pages-profile-settings.html">
                <span class="badge bg-success-subtle text-success mt-1 float-end">New</span>
                <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Settings</span>
            </a>
            <a class="dropdown-item" href="auth-lockscreen-basic.html">
                <i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Lock screen</span>
            </a>
            <a class="dropdown-item" href="auth-logout-basic.html">
                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle" data-key="t-logout">Logout</span>
            </a>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title">
                    <span data-key="t-menu">
                        <div class="form-check form-switch form-switch-secondary m-3 d-flex justify-content-between align-items-center">
                            <label class="text-body-secondary form-check-label mb-0" for="SwitchCheck10">menu</label>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="SwitchCheck10" checked>
                        </div>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link ajax-link" href="{{route('dashboard')}}">
                        <i class="ri-dashboard-2-line"></i>
                        <span>Dashboards</span>
                    </a>
                </li>
                @if(Auth::check() && (Auth::user()->hasRole('seller') || Auth::user()->hasRole('product_manager')))
                <li class="nav-item">
                    <a class="nav-link menu-link ajax-link" href="{{route('order_si')}}">
                        <i class=" ri-shopping-bag-3-line"></i>
                        <span>Quản lý đơn hàng</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link ajax-link" data-key="t-chat" href="{{route('order_si')}}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class=" ri-shopping-bag-3-line"></i>
                        <span data-key="t-apps">Quản Lý đơn hàng</span>
                    </a>
                    <div class="collapse menu-dropdown" id="donhang">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('order_si')}}" class="nav-link ajax-link" data-key="t-chat">Đơn hàng sỉ</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('quang_cao_shop')}}" class="nav-link ajax-link" data-key="t-chat">Quảng cáo</a>
                            </li>
                        </ul>
                    </div>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#thanhtoan" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-money-dollar-circle-line"></i>
                        <span data-key="t-apps">Tài chính</span>
                    </a>
                    <div class="collapse menu-dropdown" id="thanhtoan">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="" class="nav-link ajax-link" id="openNapTienModal"><span style="font-size: 12px;padding: 5px 10px; background-color: #1a90f4; color: #fff; border-radius: 7px;">+ Nạp tiền</span></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('transaction') }}" class="nav-link ajax-link" data-key="t-chat">Lịch sử giao dịch</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('balance.history') }}" class="nav-link ajax-link" data-key="t-chat">Biến động số dư</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link ajax-link" href="{{ route('settlement.settlement-report') }}">
                                    <span data-key="t-settlement-report">Báo cáo quyết toán</span>
                                </a>
                            </li>
                            <!--
                            <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#Push_sp" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-archive-fill"></i>
                        <span data-key="t-apps">Dịch Vụ DropShip</span>
                    </a>
                    <div class="collapse menu-dropdown" id="Push_sp">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('quang_cao_shop')}}" class="nav-link ajax-link" data-key="t-chat">Quảng cáo</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('list_program')}}" class="nav-link ajax-link" data-key="t-chat">Gói đăng sản phẩm</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a href="{{ route('check_so_dt') }}" class="nav-link" data-key="t-chat">Check số điện thoại</a>
                            </li> -->
                            <li class="nav-item">
                                <a href="{{route('campaign')}}" class="nav-link ajax-link" data-key="t-horizontal">Tính % chiến dịch</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @if(Auth::check() && Auth::user()->hasRole('product_manager'))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#tao_dang_sp_pm" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="tao_dang_sp_pm">
                        <i class="ri-apps-2-line"></i>
                        <span data-key="t-apps">Tạo & Đăng sản phẩm</span>
                    </a>
                    <div class="collapse menu-dropdown" id="tao_dang_sp_pm">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('program_view')}}" class="nav-link ajax-link" data-key="t-main-calender">Tạo gói</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('procerssing')}}" class="nav-link ajax-link">Danh sách gói</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @endif
                @if(Auth::check() && Auth::user()->hasRole('admin'))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i>
                        <span data-key="t-layouts">Quản lý khách hàng</span>

                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('Get_all')}}" class="nav-link ajax-link" data-key="t-horizontal">Thông tin khách hàng</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('naptien_khach_hang') }}">Nạp tiền cho khách hàng</a>
                            </li> -->
                            <!-- <li class="nav-item">
                                <a href="{{route('transaction_all')}}" class="nav-link ajax-link" data-key="t-chat">Lịch sử nạp </a>
                            </li> -->
                            <!-- <li class="nav-item">
                                <a href="{{route('Get_orders_all')}}" class="nav-link ajax-link" data-key="t-detached">Tất cả đơn hàng</a>
                            </li> -->
                           
                            <li class="nav-item">
                                <a href="{{route('get_all_transaction.list')}}" class="nav-link ajax-link" data-key="t-detached">Nạp tiền & lịch sử nạp</a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarCalendar" class="nav-link " data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCalendar" data-key="t-calender">
                                    Đơn hàng
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarCalendar">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('Get_orders_all')}}" class="nav-link ajax-link" data-key="t-main-calender">Tất cả</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{route('Overdue_Order')}}" class="nav-link ajax-link">Đơn chậm</a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a href="{{route('list_program')}}" class="nav-link" data-key="t-month-grid">Danh sách sản phẩm</a>
                                        </li> -->
                                    </ul>
                                </div>
                            </li>
                            <!-- <li class="nav-item">
                                <a href="{{route('Overdue_Order')}}" class="nav-link ajax-link" data-key="t-chat">Đơn hàng trễ thanh toán </a>

                            </li> -->
                            
                            <!-- <li class="nav-item">
                                <a href="{{route('shops')}}" class="nav-link ajax-link" data-key="t-chat">Shop</a>
                            </li> -->
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#quyettoan" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i>
                        <span data-key="t-layouts">Quyết toán</span>

                    </a>
                    <div class="collapse menu-dropdown" id="quyettoan">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('order.import_don_hoan')}}" class="nav-link ajax-link" data-key="t-chat">Cập nhật đơn hoàn</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('user-monthly-reports.index')}}" class="nav-link ajax-link" data-key="t-horizontal">Cập nhật quyết toán</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i>
                        <span data-key="t-apps">Quản lý dịch vụ Drops</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarApps">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#sidebarCalendar" class="nav-link " data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCalendar" data-key="t-calender">
                                    Tạo & Đăng sản phẩm
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarCalendar">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('program_view')}}" class="nav-link ajax-link" data-key="t-main-calender">Tạo gói</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{route('procerssing')}}" class="nav-link ajax-link">Danh sách gói</a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a href="{{route('list_program')}}" class="nav-link" data-key="t-month-grid">Danh sách sản phẩm</a>
                                        </li> -->
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarCalendar" class="nav-link " data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCalendar" data-key="t-calender">
                                    Quản lý quảng cáo
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarCalendar">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('quang-cao')}}" class="nav-link ajax-link" data-key="t-chat"> Thêm mới</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{route('quang_cao_all')}}" class="nav-link ajax-link" data-key="t-chat"> Tất Cả</a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a href="{{route('list_program')}}" class="nav-link" data-key="t-month-grid">Danh sách sản phẩm</a>
                                        </li> -->
                                    </ul>
                                </div>
                            </li>


                            <!-- <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('get_SI_transaction.list') }}" class="nav-link ajax-link menu-link" data-key="t-detached">
                        <i class="ri-layout-3-line"></i>
                        <span data-key="t-detached">Thanh toán nhập hàng</span>
                    </a>
                </li>

                <li class="nav-item">

                    <a class="nav-link menu-link" href="#hethong" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i>
                        <span data-key="t-apps">Hệ thống</span>
                    </a>
                    <div class="collapse menu-dropdown" id="hethong">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.balance_issues.index') }}" class="nav-link ajax-link" data-key="t-chat">Lỗi số dư AI</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('productsss')}}" class="nav-link ajax-link" data-key="t-horizontal">Công cụ</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link ajax-link" href="{{route('view_total_bill')}}">
                        <i class="ri-apps-2-line"></i>
                        <span>Phí Drop & Gói sản phẩm</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a href="{{route('view_total_bill')}}" class="nav-link ajax-link">Phí Drop & Gói sản phẩm</a>
                </li> -->


                <!-- <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
            </ul>
        </div>
        </li>
        @endif
        <!-- <li class="nav-item">
            <a class="nav-link menu-link" href="#congcu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                <i class="ri-layout-3-line"></i>
                <span data-key="t-layouts">Công cụ tiện ích</span>

            </a>
            <div class="collapse menu-dropdown" id="congcu">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="{{route('campaign')}}" class="nav-link ajax-link" data-key="t-horizontal">Tính phần trăm chiến dịch</a>
                    </li>
                </ul>
            </div>
        </li> -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="{{ route('affiliate.affiliate') }}" target="_blank">
                <i class="ri-group-line"></i>
                <span data-key="t-affiliate">Chương trình Affiliate</span>
            </a>
        </li> -->

        </ul>
        <!-- <div class="card-body d-flex flex-column align-items-center">
            <a href="https://www.facebook.com/groups/1374113646667338" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 mt-2 pt-2">
                <i class="bi bi-facebook fs-5"></i> Tham gia nhóm Facebook
            </a>
        </div> -->

    </div>


</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#openNapTienModal').click(function() {
            // Đóng modal cũ nếu còn đang mở (chuẩn Bootstrap)
            if ($('#napTienModal').length) {
                const napModalInstance = bootstrap.Modal.getInstance(document.getElementById('napTienModal'));
                if (napModalInstance) napModalInstance.hide();
            }
            if ($('#qrModal').length) {
                const qrModalInstance = bootstrap.Modal.getInstance(document.getElementById('qrModal'));
                if (qrModalInstance) qrModalInstance.hide();
            }

            // Đợi modal đóng xong rồi mới xóa khỏi DOM
            $(document).one('hidden.bs.modal', function() {
                $('#napTienModal').remove();
                $('#qrModal').remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

            // Nếu không có modal nào đang mở thì xóa luôn
            if (!$('#napTienModal').length && !$('#qrModal').length) {
                $('#napTienModal').remove();
                $('#qrModal').remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }

            // Gọi AJAX lấy modal mới
            $.get('{{ route("naptien") }}', function(data) {
                // Tạo một div tạm để parse HTML
                var $tmp = $('<div>').html(data);
                // Lấy script trong modal và thực thi (không cần nữa vì đã đưa ra ngoài)
                // Append phần HTML (không bao gồm script) vào body
                $('body').append($tmp.contents().not('script'));
                if (typeof window.initNapTienModalEvents === 'function') {
                    window.initNapTienModalEvents();
                }
                const napTienModal = new bootstrap.Modal(document.getElementById('napTienModal'));
                napTienModal.show();
            });
        });

        const switchId = '#SwitchCheck10';
        const menuClass = '.menu-dropdown';
        const linkClass = '.menu-link';

        // Lấy ID người dùng từ backend (Laravel)
        const userId = '{{ Auth::id() }}'; // Lấy ID người dùng hiện tại
        const storageKey = `expandAll_${userId}`; // Tạo khóa duy nhất cho từng người dùng

        // Khôi phục trạng thái từ localStorage
        const expandAll = localStorage.getItem(storageKey) === 'true';
        $(switchId).prop('checked', expandAll);
        if (expandAll) {
            $(menuClass).addClass('show');
            $(linkClass).attr('aria-expanded', 'true');
        } else {
            $(menuClass).removeClass('show');
            $(linkClass).attr('aria-expanded', 'false');
        }

        // Lắng nghe sự kiện thay đổi trạng thái của switch
        $(switchId).change(function() {
            const isChecked = $(this).is(':checked');

            // Mở hoặc đóng tất cả menu
            if (isChecked) {
                $(menuClass).addClass('show');
                $(linkClass).attr('aria-expanded', 'true');
            } else {
                $(menuClass).removeClass('show');
                $(linkClass).attr('aria-expanded', 'false');
            }

            // Lưu trạng thái vào localStorage với khóa dựa trên user ID
            localStorage.setItem(storageKey, isChecked);
        });
    });
</script>
<script src="{{ asset('assets/js/naptien.js') }}"></script>